<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Shipping;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ShippingController extends Controller
{
    public function index()
    {
        try {
            $shippings = Shipping::with(['order', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $shippings
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching shippings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shippings'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'shipping_method' => 'required|string',
                'tracking_number' => 'required|string|unique:shippings',
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string',
                'shipping_state' => 'required|string',
                'shipping_country' => 'required|string',
                'shipping_postal_code' => 'required|string',
                'estimated_delivery_date' => 'required|date',
                'notes' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $shipping = Shipping::create([
                'order_id' => $validated['order_id'],
                'shipping_method' => $validated['shipping_method'],
                'tracking_number' => $validated['tracking_number'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_country' => $validated['shipping_country'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'estimated_delivery_date' => $validated['estimated_delivery_date'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => $validated['created_by'] ?? null,
                'updated_by' => $validated['updated_by'] ?? null
            ]);

            // Update order status
            $order = Order::findOrFail($validated['order_id']);
            $order->update(['status' => 'shipped']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipping created successfully',
                'data' => $shipping->load('order')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipping'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $shipping = Shipping::with(['order', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $shipping
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Shipping not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $shipping = Shipping::findOrFail($id);

            $validated = $request->validate([
                'shipping_method' => 'required|string',
                'tracking_number' => 'required|string|unique:shippings,tracking_number,' . $id,
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string',
                'shipping_state' => 'required|string',
                'shipping_country' => 'required|string',
                'shipping_postal_code' => 'required|string',
                'estimated_delivery_date' => 'required|date',
                'status' => 'required|string|in:pending,in_transit,delivered,failed',
                'notes' => 'nullable|string',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $shipping->update($validated);

            // Update order status if shipping is delivered
            if ($validated['status'] === 'delivered') {
                $order = $shipping->order;
                $order->update(['status' => 'delivered']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipping updated successfully',
                'data' => $shipping->load('order')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $shipping = Shipping::findOrFail($id);
            $order = $shipping->order;

            $shipping->delete();
            $order->update(['status' => 'processing']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipping deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipping'
            ], 500);
        }
    }

    public function track($trackingNumber)
    {
        try {
            $shipping = Shipping::where('tracking_number', $trackingNumber)
                ->with(['order', 'order.customer'])
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'tracking_number' => $shipping->tracking_number,
                    'status' => $shipping->status,
                    'estimated_delivery_date' => $shipping->estimated_delivery_date,
                    'shipping_address' => $shipping->shipping_address,
                    'shipping_city' => $shipping->shipping_city,
                    'shipping_state' => $shipping->shipping_state,
                    'shipping_country' => $shipping->shipping_country,
                    'shipping_postal_code' => $shipping->shipping_postal_code,
                    'order_details' => [
                        'order_id' => $shipping->order->id,
                        'customer_name' => $shipping->order->customer->name,
                        'order_date' => $shipping->order->created_at
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking shipping: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Shipping not found'
            ], 404);
        }
    }
} 