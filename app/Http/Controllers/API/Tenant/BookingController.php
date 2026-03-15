<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\Contact;
use App\Models\TableReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function showSubscriberEmail(Request $request)
    {
        try {
            $query = Subscriber::query();
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where('email', 'like', "%$search%") ;
            }
            $perPage = $request->get('per_page', 8);
            $subscribers = $query->orderByDesc('id')->paginate($perPage);
            return response()->json([
                'success' => true,
                'data' => $subscribers
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch subscribers. Try again later.'
            ], 500);
        }
    }

    public function showContactInfo(Request $request)
    {
        try {
            $query = Contact::query();
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%")
                      ->orWhere('subject', 'like', "%$search%")
                      ->orWhere('message', 'like', "%$search%")
                    ;
                });
            }
            $perPage = $request->get('per_page', 8);
            $contacts = $query->orderByDesc('id')->paginate($perPage);
            return response()->json([
                'success' => true,
                'data' => $contacts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch contact requests. Try again later.'
            ], 500);
        }
    }

    public function showReservationInfo(Request $request)
    {
        try {
            $query = TableReservation::query();
            
            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone_number', 'like', "%$search%")
                      ->orWhere('message', 'like', "%$search%")
                      ->orWhere('date', 'like', "%$search%")
                      ->orWhere('time', 'like', "%$search%")
                      ->orWhere('guests', 'like', "%$search%")
                      ->orWhere('table_no', 'like', "%$search%")
                    ;
                });
            }

            // Status filter
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Date filter
            if ($request->has('date') && $request->date) {
                $query->where('date', $request->date);
            }

            $perPage = $request->get('per_page', 8);
            $reservations = $query->orderByDesc('id')->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $reservations
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not fetch reservations. Try again later.'
            ], 500);
        }
    }

    public function storeReservationInfo(Request $request)
    {
        try {
            Log::info('Starting reservation process...');
            $validated = $request->validate([
                'email' => 'required|email',
                'full_name' => 'required|string',
                'date' => 'required|string',
                'time' => 'required|string',
                'guests' => 'required|integer',
                'phone_number' => 'required|string',
                'message' => 'nullable|string',
            ]);
            Log::info('validation added');

            // Check for duplicate reservations
            $existingReservation = TableReservation::where('email', $validated['email'])
                ->where('date', $validated['date'])
                ->where('time', $validated['time'])
                ->whereIn('status', ['pending', 'confirmed', 'assigned'])
                ->first();

            if ($existingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a reservation for this date and time.'
                ], 409);
            }

            $tableReservation = TableReservation::create(array_merge($validated, [
                'status' => 'pending'
            ]));
            Log::info('saved');
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation submitted successfully.',
                'data' => $tableReservation
            ], 201);
        } catch (\Exception $e) {
            Log::error('Reservation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not submit reservation. Try again later.'
            ], 500);
        }
    }

    /**
     * Assign table to reservation
     */
    public function assignTable(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'table_no' => 'required|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reservation = TableReservation::findOrFail($id);
            
            // Check if table is already assigned to another reservation at the same time
            $conflictingReservation = TableReservation::where('table_no', $request->table_no)
                ->where('date', $reservation->date)
                ->where('time', $reservation->time)
                ->where('id', '!=', $id)
                ->whereIn('status', ['confirmed', 'assigned', 'pending'])
                ->first();

            if ($conflictingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table is already assigned to another reservation at the same time.'
                ], 409);
            }

            $reservation->update([
                'table_no' => $request->table_no,
                'status' => 'assigned',
                'assigned_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => 'Table assigned successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Assign table error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign table. Please try again.'
            ], 500);
        }
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,assigned,seated,completed,cancelled,no_show',
                'table_no' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reservation = TableReservation::findOrFail($id);
            
            $updateData = ['status' => $request->status];
            
            // If table number is provided, update it
            if ($request->has('table_no')) {
                $updateData['table_no'] = $request->table_no;
            }

            // If status is assigned and table_no is provided, set assigned_at
            if ($request->status === 'assigned' && $request->table_no) {
                $updateData['assigned_at'] = now();
            }
            
            // If status is seated, record the time
            if ($request->status === 'seated') {
                $updateData['seated_at'] = now();
            }
            
            // If status is completed, record the time
            if ($request->status === 'completed') {
                $updateData['completed_at'] = now();
            }

            $reservation->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => 'Reservation status updated successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Update status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reservation status. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete reservation
     */
    public function destroy($id)
    {
        try {
            $reservation = TableReservation::findOrFail($id);
            $reservation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Reservation deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete reservation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete reservation. Please try again.'
            ], 500);
        }
    }

    /**
     * Get single reservation details
     */
    public function show($id)
    {
        try {
            $reservation = TableReservation::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $reservation,
                'message' => 'Reservation retrieved successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found.'
            ], 404);
        }
    }
}