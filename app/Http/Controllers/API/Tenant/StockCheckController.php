<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\StockCheckReq;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockCheckController extends Controller
{
    public function index(Request $request)
    {
        // dd('here'); 
     
        try {
            $query = StockCheckReq::with(['product']);
            
            // Apply filters
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('special_instructions', 'like', "%{$search}%")
                      ->orWhere('admin_notes', 'like', "%{$search}%")
                      ->orWhereHas('product', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }
            

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            $stockChecks = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);
            
            // dd($stockChecks);
            return response()->json([
                'success' => true,
                'data' => $stockChecks
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching stock checks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock checks'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'special_instructions' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
            ]);

            $product = Product::findOrFail($validated['product_id']);
            
            $stockCheck = StockCheckReq::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'status' => 'pending',
                'created_by' => $validated['created_by'] ?? auth()->id(),
                'in_stock' => $product->stock_quantity >= $validated['quantity'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock check request created successfully',
                'data' => $stockCheck->load('product')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating stock check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create stock check'
            ], 500);
        }
    }

    // New API for updating status
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,available,out_of_stock,completed',
                'admin_notes' => 'nullable|string',
                'notify_user' => 'boolean'
            ]);

            $stockCheck = StockCheckReq::findOrFail($id);
            
            $stockCheck->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                'status_updated_at' => now(),
                // 'updated_by' => auth()->id()
            ]);

            // Here you can add email notification logic if notify_user is true
            if ($request->notify_user) {
                // Send email notification to user
                // Mail::to($userEmail)->send(new StockCheckStatusUpdated($stockCheck));
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => $stockCheck->load('product')
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());       
            Log::error('Error updating stock check status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $stockCheck = StockCheckReq::with(['product'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $stockCheck
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stock check not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $stockCheck = StockCheckReq::findOrFail($id);

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'special_instructions' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $product = Product::findOrFail($validated['product_id']);

            $stockCheck->update([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'updated_by' => $validated['updated_by'] ?? auth()->id(),
                'in_stock' => $product->stock_quantity >= $validated['quantity'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock check updated successfully',
                'data' => $stockCheck->load('product')
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating stock check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock check'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $stockCheck = StockCheckReq::findOrFail($id);
            $stockCheck->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock check deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting stock check: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete stock check'
            ], 500);
        }
    }
}