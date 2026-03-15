<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryBoyController extends Controller
{
    /**
     * Get available delivery boys within range
     */
public function getAvailableDeliveryBoys(Request $request)
{
    try {
        // Just get all delivery boys with is_in_range = 1 for testing
        $deliveryBoys = User::where('is_in_range', 1)
            ->where('is_in_range', 1)
            ->withWhereHas('roles', function ($query) {
                $query->where('name', 'delivery_boy');
            })
            ->where('is_available_for_delivery', 1)
            ->limit(20)
            ->get()
            ->map(function ($boy) {
                // Add dummy distance for testing
                $boy->distance = rand(1, 10) / 10; // Random distance 0.1 to 1.0 km
                $boy->within_range = true;
                return $boy;
            });

        return response()->json([
            'success' => true,
            'data' => $deliveryBoys,
            'debug' => 'Testing mode - returning all in_range users'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error fetching delivery boys: ' . $e->getMessage());
        dd($e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch delivery boys'
        ], 500);
    }
}
    /**
     * Get all delivery boys
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search', '');
            
            $deliveryBoys = User::where('role', 'delivery_boy')
                ->with(['currentOrder'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $deliveryBoys
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching delivery boys: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch delivery boys'
            ], 500);
        }
    }

    /**
     * Update delivery boy status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'is_in_range' => 'required|boolean'
        ]);

        try {
            $deliveryBoy = User::where('id', $id)
                ->where('role', 'delivery_boy')
                ->firstOrFail();

            $deliveryBoy->is_in_range = $request->is_in_range;

            // If making unavailable, clear current order
            if (!$request->is_in_range) {
                $deliveryBoy->current_order_id = null;
            }
            
            $deliveryBoy->save();

            return response()->json([
                'success' => true,
                'message' => 'Delivery boy status updated successfully',
                'data' => $deliveryBoy
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating delivery boy status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery boy status'
            ], 500);
        }
    }

    /**
     * Update delivery boy location
     */
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        try {
            $deliveryBoy = User::where('id', $id)
                ->where('role', 'delivery_boy')
                ->firstOrFail();
            
            $deliveryBoy->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'last_location_update' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating delivery boy location: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location'
            ], 500);
        }
    }

    /**
     * Get delivery boy statistics
     */
    public function getStats($id)
    {
        try {
            $deliveryBoy = User::where('id', $id)
                ->where('role', 'delivery_boy')
                ->firstOrFail();

            $stats = [
                'total_orders' => Order::where('delivery_boy_id', $id)->count(),
                'completed_orders' => Order::where('delivery_boy_id', $id)
                    ->where('status', 'delivered')
                    ->count(),
                'pending_orders' => Order::where('delivery_boy_id', $id)
                    ->whereIn('status', ['assigned', 'picked_up'])
                    ->count(),
                'today_orders' => Order::where('delivery_boy_id', $id)
                    ->whereDate('delivery_assigned_at', today())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'delivery_boy' => $deliveryBoy,
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching delivery boy stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch delivery boy statistics'
            ], 500);
        }
    }


    /**
 * Delivery boy updates availability status
 */
public function updateAvailability(Request $request)
{
    // dd($request->all());
    try {
        $deliveryBoy = auth()->user();
        $deliveryBoy->is_available_for_delivery = $request->available;
        $deliveryBoy->save();

        return response()->json([
            'success' => true,
            'message' => $request->available ? 'You are now available for deliveries' : 'You are now unavailable',
            'data' => [
                'is_available_for_delivery' => $deliveryBoy->is_available_for_delivery,
                'current_delivery_count' => $deliveryBoy->current_delivery_count
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update availability'
        ], 500);
    }
}

/**
 * Enhanced status update that also manages availability
 */
public function updateDeliveryStatus(Request $request)
{
    // dd($request->all());
    // DB::beginTransaction();
    
    try {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:accepted,picked_up,out_for_delivery,delivered,cancelled'
        ]);

        $order = Order::findOrFail($request->order_id);
        $deliveryBoy = auth()->user();

        // Check if delivery boy owns this order
        if ($order->delivery_boy_id !== $deliveryBoy->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this order'
            ], 403);
        }

        $oldStatus = $order->delivery_status;
        
        // Update status based on the action
        switch ($request->status) {
            case 'accepted':
                $order->status = 'accepted';
                $order->delivery_accepted_at = now();
                $order->save();
                break;

            case 'picked_up':
                $order->status = 'picked_up';
                $order->delivery_started_at = now();
                $order->save();
                break;

            case 'out_for_delivery':
                $order->status = 'out_for_delivery';
                $order->delivery_started_at = now();
                $deliveryBoy->is_available_for_delivery = 0;
                $deliveryBoy->save();
                $order->save();
                break;

            case 'delivered':
                $order->status = 'delivered';
                $order->delivered_at = now();
                $order->status = 'completed';
                // Free up delivery boy capacity
                $order->save();
                // $deliveryBoy->decrementDeliveryCount();
                break;

            case 'cancelled':
                $order->status = 'cancelled';
                // Free up delivery boy capacity
                // $deliveryBoy->decrementDeliveryCount();
                $order->save();
                break;
        }

        $order->save();

        // DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to: ' . $request->status,
            'data' => [
                'order_id' => $order->id,
                'delivery_status' => $order->delivery_status,
                'current_delivery_count' => $deliveryBoy->current_delivery_count,
                'can_accept_more' => $deliveryBoy->current_delivery_count < 3
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to update status: ' . $e->getMessage()
        ], 500);
    }
}

public function getMyOrders()
    {
        
        try {
            $deliveryBoy = auth()->user();
            
            $orders = Order::where('delivery_boy_id', $deliveryBoy->id)
                ->whereIn('status', ['assigned', 'accepted', 'picked_up', 'out_for_delivery', 'completed'])
                ->with(['customer'])
                ->orderBy('delivery_assigned_at', 'desc')
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer->name,
                        'customer_phone' => $order->customer->phone,
                        'delivery_address' => $order->delivery_address,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'assigned_at' => $order->delivery_assigned_at,
                        // 'can_update' => $order->canBeUpdatedByDeliveryBoy()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }



    public function updateOrderStatus(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'status' => 'required|in:assigned,accepted,picked_up,out_for_delivery,delivered,cancelled'
    ]);

    try {
        $deliveryBoyId = auth()->id();
        $order = Order::where('id', $request->order_id)
                    ->where('delivery_boy_id', $deliveryBoyId)
                    ->firstOrFail();

        $order->delivery_status = $request->status;
        $order->save();

        // Get updated delivery count
        $currentDeliveryCount = Order::where('delivery_boy_id', $deliveryBoyId)
            ->whereNotIn('delivery_status', ['delivered', 'cancelled'])
            ->count();

        $canAcceptMore = $currentDeliveryCount < 3;

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => [
                'current_delivery_count' => $currentDeliveryCount,
                'can_accept_more' => $canAcceptMore
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating order status: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to update order status'
        ], 500);
    }
}

// delivery boy profile
public function getProfile()
{
    try {
        $deliveryBoy = auth()->user();
        return response()->json([
            'success' => true,
            'data' => $deliveryBoy
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching delivery boy profile: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch delivery boy profile'
        ], 500);
    }
}
}
?>