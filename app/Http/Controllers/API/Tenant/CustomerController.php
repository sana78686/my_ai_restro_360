<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::with(['creator', 'updater'])
                ->when($request->search, function ($q) use ($request) {
                    return $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                });

            $customers = $query->orderBy('name')
                ->paginate($request->per_page ?? 10);

            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching customers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $customer = Customer::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer->load(['creator', 'updater'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer'
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $customer->load(['creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer'
            ], 500);
        }
    }

    public function update(Request $request, Customer $customer)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:customers,email,' . $customer->id,
                'phone' => 'sometimes|required|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'notes' => 'nullable|string',
                'status' => 'sometimes|required|in:active,inactive'
            ]);

            $validated['updated_by'] = auth()->id();

            $customer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer->load(['creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer'
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has any orders
            if ($customer->orders()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer as they have associated orders'
                ], 400);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            
            // Check if customer has any orders
            if ($customer->orders()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer as they have associated orders'
                ], 400);
            }

            $customer->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Customer permanently deleted'
            ]);
        } catch (\Exception $e) {
            Log::error('Error force deleting customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete customer'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $customer = Customer::withTrashed()->findOrFail($id);
            $customer->restore();

            return response()->json([
                'success' => true,
                'message' => 'Customer restored successfully',
                'data' => $customer->load(['creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore customer'
            ], 500);
        }
    }

    public function getOrCreateCustomer(Request $request)
    {
        $ipAddress = $request->ip();
        $uniqueId = session('customer_unique_id');

        if (!$uniqueId) {
            $uniqueId = Str::uuid()->toString();
            session(['customer_unique_id' => $uniqueId]);
        }

        $customer = Customer::firstOrCreate(
            ['unique_id' => $uniqueId],
            [
                'ip_address' => $ipAddress,
                'restaurant_id' => $request->restaurant_id,
                'is_guest' => true
            ]
        );

        return $customer;
    }

    public function updateCustomer(Request $request)
    {
        $uniqueId = session('customer_unique_id');
        $customer = Customer::where('unique_id', $uniqueId)->firstOrFail();

        $customer->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_guest' => false
        ]);

        return response()->json($customer);
    }
} 