<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\MailLog;
use App\Models\Product;
use App\Models\MailLogs;
use App\Models\OrderItem;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\API\Tenant\CustomerController;

class OrderController extends Controller
{
    protected $customerController;

    public function __construct(CustomerController $customerController)
    {
        $this->customerController = $customerController;
    }

    /**
     * Generate order number: 5 digits + 1 alphabet letter
     * Example: 12345A, 67890Z
     */
    private function generateOrderNumber(): string
    {
        // Generate 5 random digits
        $digits = str_pad((string) rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        
        // Generate 1 random uppercase letter
        $letter = chr(rand(65, 90)); // A-Z
        
        return $digits . $letter;
    }

    public function placeOrder(Request $request)
    {
        // dd($request->all());
        $customer = $this->customerController->getOrCreateCustomer($request);
        $cartItems = Cart::with('product')
            ->where('customer_unique_id', $customer->unique_id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $totalAmount = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $order = Order::create([
            'tracking_id' => 'ORD-' . Str::random(10),
            'customer_id' => $customer->id,
            'restaurant_id' => $request->restaurant_id,
            'total_amount' => $totalAmount,
            'delivery_address' => $request->delivery_address,
            'special_instructions' => $request->special_instructions,
            'payment_method' => $request->payment_method
        ]);

        foreach ($cartItems as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'options' => $item->options
            ]);
        }

        
        // Clear the cart after order is placed
        Cart::where('customer_unique_id', $customer->unique_id)->delete();

        return response()->json($order);
    }

    public function getOrderDetails(Order $order)
    {
        $order->load(['orderDetails.product', 'customer']);
        return response()->json($order);
    }

    public function updateOrderStatus(Order $order, Request $request)
    {
        $order->update(['status' => $request->status]);
        return response()->json($order);
    }

    public function getCustomerOrders(Request $request)
    {
        $customer = $this->customerController->getOrCreateCustomer($request);
        $orders = Order::with(['orderDetails.product'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function index(Request $request)
    {
        try {
            $query = Order::with(['customer', 'orderDetails', 'orderDetails.product'])
                ->when($request->customer_id, function ($q) use ($request) {
                    return $q->where('customer_id', $request->customer_id);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->when($request->date_from, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->date_to, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->date_to);
                })
                ->when($request->search, function ($q) use ($request) {
                    return $q->where('order_number', 'like', '%' . $request->search . '%')
                        ->orWhereHas('customer', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->search . '%');
                        });
                });

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 10);
            // dd($orders);
            Log::info("orders", [$orders]);
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            // Generate order number: 5 digits + 1 alphabet letter
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $validated['customer_id'],
                'notes' => $validated['notes'],
                'status' => $validated['status'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Create order items and update product stock
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Check if product has enough stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Create order items
                $orderItem = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price']
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);

                $totalAmount += $orderItem->total;
            }

            // Update order total
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load(['items.product', 'customer', 'creator', 'updater'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Order $order)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $order->load(['items.product', 'customer', 'creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order'
            ], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'status' => 'sometimes|required|in:pending,processing,completed,cancelled',
                'notes' => 'nullable|string'
            ]);

            // If order is being cancelled, restore product stock
            if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    $product->increment('stock_quantity', $item->quantity);
                }
            }

            $validated['updated_by'] = auth()->id();
            $order->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order->load(['items.product', 'customer', 'creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order'
            ], 500);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // Restore product stock
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->increment('stock_quantity', $item->quantity);
            }

            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::withTrashed()->findOrFail($id);

            // Delete order items
            $order->items()->forceDelete();

            // Delete order
            $order->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order permanently deleted'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete order'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::withTrashed()->findOrFail($id);

            // Restore order items
            $order->items()->restore();

            // Restore order
            $order->restore();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order restored successfully',
                'data' => $order->load(['items.product', 'customer', 'creator', 'updater'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore order'
            ], 500);
        }
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     $status = $request->query('status'); // or $request->get('status')

    //     $request->validate([
    //         'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled'])]
    //     ]);

    //     $tenant = Order::findOrFail($id);
    //     $tenant->status = $status;
    //     $tenant->save();

    //      Mail::to($tenant->customer_email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus));
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Tenant status updated successfully',
    //         'data' => $tenant
    //     ]);
    // }


//     public function updateStatus(Request $request, $id)
//     {
//         // Get status from request (query parameter or request body)
//         $status = $request->get('status', $request->query('status'));

//         $request->validate([
//             'status' => ['required', Rule::in(['preparing', 'preparation-done', 'pending', 'processing', 'completed', 'cancelled'])]
//         ]);

//         // Find the order (not tenant)
//         $order = Order::findOrFail($id);

//         // Store the old status before updating
//         $oldStatus = $order->status;
//         // dd($order->customer->email);
//         // Update the order status
//         $newStatus = $order->status = $status;
//         $order->save();

//         // mail to specific users notification also
//         $emails = User::Role(['manager', 'order_taker', 'kitchen', 'delivery_boy'])->pluck('email')->toArray();
//         // dd($order);
//         // Mail::to($emails)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus));

//         $users = User::role(['manager', 'order_taker', 'kitchen', 'delivery_boy'])->get();


//         foreach ($users as $user) {
//             Mail::to($user->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus, $user));
//         }
//         if ($status === 'completed' || $status === 'cancelled') {
//             $emails = User::Role(['manager', 'order_taker', 'kitchen', 'delivery_boy'])->pluck('email')->toArray();
//             Mail::to($emails)->send(new OrderStatusUpdated($order, $oldStatus, $status, $user));
//             // send mail to customer also 
//             Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $oldStatus, $status, $user));
//         }

//         // Notify customer
//         $notification = Notification::create([
//             'type' => 'order-update',
//             'title' => 'Order Status Updated!',
//             'message' => 'Your order has been '.$status,
//             'is_read' => false,
//             // 'user_id' => $user->id, // Replace with the correct user variable
//         ]);
// // send to roles admin manager 
//         $notification->users()->attach(
//             User::role(['restaurant_owner', 'manager', 'kitchen'])->pluck('id')->toArray()
//         );


//         // Notify staff (manager/kitchen/etc.)
//         // foreach (User::role(['manager', 'kitchen', 'order_taker', 'delivery_boy'])->get() as $user) {
//         //     Notification::create([
//         //         'type' => 'order',
//         //         'order_id' => $order->id,
//         //         'title' => 'Order Status Changed',
//         //         'message' => 'Order #' . $order->order_number . ' status changed to ' . ucfirst($newStatus) . '.',
//         //         'is_read' => false,
//         //         'user_id' => $user->id,
//         //     ]);
//         // }
//         MailLogs::logMail(
//             sendBy: Auth::user()->email ?? 'system',
//             sentTo: $order->customer->email,
//             content: view('emails.order_status_updated', [
//                 'order' => $order,
//                 'oldStatus' => $oldStatus,
//                 'newStatus' => $status,
//             ])->render(),  // ✅ full HTML rendered
//             mailType: 'order_type'
//         );


//         return response()->json([
//             'success' => true,
//             'message' => 'Order status updated successfully',
//             'data' => $order
//         ]);
//     }

public function updateStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => ['required', Rule::in([
            'pending', 'processing', 'preparing', 'preparation-done', 'completed', 'cancelled'
        ])],
    ]);

    $newStatus = $validated['status'];

    // 🔹 Find the order
    $order = Order::findOrFail($id);
    $oldStatus = $order->status;

    // 🔹 Update order status
    $order->update(['status' => $newStatus]);

    // 🔹 Determine who to notify (staff roles)
    $staffRoles = ['restaurant_owner', 'manager', 'kitchen', 'order_taker', 'delivery_boy'];
    $staffUsers = User::role($staffRoles)->get();

    // 🔹 Send notification emails to staff
    foreach ($staffUsers as $user) {
        Mail::to($user->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus, $user));
    }

    // 🔹 Also notify customer if order completed or cancelled
    if (in_array($newStatus, ['completed', 'cancelled']) && $order->customer?->email) {
        Mail::to($order->customer->email)->send(new OrderStatusUpdated($order, $oldStatus, $newStatus, $user));
    }

    // 🔹 Create a global notification record
    $notification = Notification::create([
        'type' => 'order-status-update',
        'title' => 'Order Status Updated',
        'message' => "Order #{$order->tracking_id} status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) . ".",
        'is_read' => false,
        'order_id' => $order->id,
    ]);

    // 🔹 Attach to staff users who should see it in dashboard
    $notification->users()->attach(
        $staffUsers->pluck('id')->toArray()
    );

    // 🔹 Log email content (for record)
    // MailLogs::logMail(
    //     sendBy: Auth::user()->email ?? 'system',
    //     sentTo: $order->customer->email ?? 'unknown',
    //     content: view('emails.order_status_updated', [
    //         'order' => $order,
    //         'oldStatus' => $oldStatus,
    //         'newStatus' => $newStatus,
    //     ])->render(),
    //     mailType: 'order_status_update'
    // );

    // ✅ Return consistent API response
    return response()->json([
        'success' => true,
        'message' => 'Order status updated successfully.',
        'data' => [
            'order' => $order,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]
    ]);
}

    public function exportInvoice($orderId)
    {
        try {
            $order = Order::with(['orderDetails.product', 'customer'])->findOrFail($orderId);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order-invoice', [
                'order'    => $order,
                'customer' => $order->customer,
                'tenant'   => tenant(),
            ])->setPaper('a4', 'portrait');

            return response()->streamDownload(
                fn() => print($pdf->output()),
                "invoice-{$order->order_number}.pdf"
            );
        } catch (\Exception $e) {
            Log::error('Invoice PDF error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage()
            ], 500);
        }
    }



    public function MailLogs(Request $request)
    {
        $query = MailLogs::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('send_by', 'like', "%{$search}%")
                    ->orWhere('sent_to', 'like', "%{$search}%")
                    ->orWhere('mail_type', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }


     /**
     * Assign delivery boy to order
     */

    public function assignDeliveryBoy(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'delivery_boy_id' => 'required|exists:users,id'
            ]);

            $order = Order::findOrFail($request->order_id);
            $deliveryBoy = User::findOrFail($request->delivery_boy_id);

            // Check if delivery boy is available
            if ($deliveryBoy->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery boy is not available at the moment'
                ], 400);
            }


            // Update order with delivery boy
            $order->delivery_boy_id = $deliveryBoy->id;
            $order->delivery_assigned_at = now();
            $deliveryBoy->is_available_for_delivery = false; //
            $deliveryBoy->save();
            $order->save();


                        // dd($order->toArray());
            // You might want to send notification to delivery boy here
            $notification =  Notification::create([
                'type' => 'delivery-assignment',
                'title' => 'New Delivery Assignment',
                'message' => 'You have been assigned to deliver order #' . $order->tracking_id,
                'is_read' => false,
                'user_id' => $deliveryBoy->id,
            ]);
            // attach it to the users that should see it in dashboard
            $notification->users()->attach(
                User::role(['delivery_boy', 'manager'])->pluck('id')->toArray()
            );

            return response()->json([
                'success' => true,
                'message' => 'Order assigned to delivery boy successfully',
                'data' => [
                    'order_id' => $order->id,
                    'delivery_boy_name' => $deliveryBoy->name,
                    'assigned_at' => $order->delivery_assigned_at
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error assigning delivery boy: ' . $e->getMessage());
            dd($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign delivery boy'
            ], 500);
        }
    }
}