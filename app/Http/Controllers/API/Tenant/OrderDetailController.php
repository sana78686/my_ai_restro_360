<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderDetailController extends Controller
{
    public function index()
    {
        try {
            $orderDetails = OrderDetail::with(['order', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $orderDetails
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order details'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'shipping_method' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'discount_amount' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $orderDetail = OrderDetail::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order detail created successfully',
                'data' => $orderDetail
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order detail'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $orderDetail = OrderDetail::with(['order', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order detail not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($id);

            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'shipping_method' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'discount_amount' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $orderDetail->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Order detail updated successfully',
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order detail'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($id);
            $orderDetail->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order detail soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error soft deleting order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete order detail'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $orderDetail = OrderDetail::withTrashed()->findOrFail($id);
            $orderDetail->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Order detail permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete order detail'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $orderDetail = OrderDetail::withTrashed()->findOrFail($id);
            $orderDetail->restore();

            return response()->json([
                'success' => true,
                'message' => 'Order detail restored successfully',
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring order detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore order detail'
            ], 500);
        }
    }
} 