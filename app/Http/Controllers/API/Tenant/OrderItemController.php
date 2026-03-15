<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    public function index()
    {
        try {
            $orderItems = OrderItem::with(['order', 'product', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $orderItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order items'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $orderItem = OrderItem::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order item created successfully',
                'data' => $orderItem
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order item'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $orderItem = OrderItem::with(['order', 'product', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $orderItem
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order item not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);

            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $orderItem->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order item updated successfully',
                'data' => $orderItem
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order item'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);
            $orderItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order item soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error soft deleting order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete order item'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $orderItem = OrderItem::withTrashed()->findOrFail($id);
            $orderItem->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Order item permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete order item'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $orderItem = OrderItem::withTrashed()->findOrFail($id);
            $orderItem->restore();

            return response()->json([
                'success' => true,
                'message' => 'Order item restored successfully',
                'data' => $orderItem
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring order item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore order item'
            ], 500);
        }
    }
} 