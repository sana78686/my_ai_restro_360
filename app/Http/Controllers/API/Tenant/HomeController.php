<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\User;
use App\Models\Order;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\MailLogs;
use App\Models\Subscriber;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Mail\OrderConfirmation;
use App\Models\TableReservation;
use App\Mail\NewOrderNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
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

    public function categories(Request $request)
    {
        try {
            $query = Category::with(['parent', 'children']);
            if ($request->has('only_deleted') && $request->only_deleted) {
                $query = $query->onlyTrashed();
            }
            if ($request->parent_id) {
                $query = $query->where('parent_id', $request->parent_id);
            }
            if ($request->search) {
                $query = $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }
            if ($request->has('is_active') && $request->is_active !== '') {
                $query = $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
            }
            $query = $query->orderBy('sort_order');
            $categories = $query->paginate($request->per_page ?? 10);

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories'
            ], 500);
        }
    }
    // public function products(Request $request)
    // {
    //     try {
    //         $query = Product::with(['category', 'creator', 'updater'])
    //             ->when($request->category_id, function ($q) use ($request) {
    //                 return $q->where('category_id', $request->category_id);
    //             })
    //             ->when($request->search, function ($q) use ($request) {
    //                 return $q->where('name', 'like', '%' . $request->search . '%')
    //                     ->orWhere('description', 'like', '%' . $request->search . '%');
    //             });

    //         // Add only_deleted support
    //         if ($request->has('only_deleted') && $request->only_deleted) {
    //             $query = $query->onlyTrashed();
    //         }

    //         if ($request->status) {
    //             $query = $query->where('status', $request->status);
    //         }

    //         $products = $query->orderBy('sort_order')->paginate($request->per_page ?? 10);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $products
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching products: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch products'
    //         ], 500);
    //     }
    // }

 public function products(Request $request)
{
    try {
        $query = Product::with(['category', 'creator', 'updater', 'media']) // Add 'media' here
            ->when($request->category_id, function ($q) use ($request) {
                return $q->where('category_id', $request->category_id);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });

        // Add only_deleted support
        if ($request->has('only_deleted') && $request->only_deleted) {
            $query = $query->onlyTrashed();
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $products = $query->orderBy('sort_order')->paginate($request->per_page ?? 10);

        // Transform the products to include images array
        $products->getCollection()->transform(function ($product) {
            $product->images = $product->getMedia('images')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getCustomProperty('public_url') ?? $media->getUrl(),
                    's3_path' => $media->getCustomProperty('s3_path'),
                    'file_name' => $media->file_name,
                ];
            })->toArray();

            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching products: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch products'
        ], 500);
    }
}
    public function storeSubscriberEmail(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:subscribers'
            ]);

            $subscribe = Subscriber::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'You have successfully subscribed to our newsletter.',
                'data' => $subscribe
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating setting: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed or already subscribed.'
            ], 500);
        }
    }
    public function storeContactInfo(Request $request)
    {
        try {
            Log::info('contact details adding');
            $validated = $request->validate([
                'email' => 'required|email',
                'name' => 'required|string',
                'subject' => 'required|string',
                'phone' => 'required|string',
                'message' => 'required|string',
            ]);
            Log::info('validation added');
            $contact = Contact::create($validated);
            Log::info('saved');

            return response()->json([
                'success' => true,
                'message' => 'You message successfully send, we will get back to you shortly.',
                'data' => $contact
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error sending contact message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send your message. Please try again later.'
            ], 500);
        }
    }

    public function storeStockCheck(Request $request)
    {
        try {
            Log::info('Starting stock check process...');
            $validated = $request->validate([
                'email' => 'required|email',
                'full_name' => 'required|string',
                'phone_number' => 'required|string',
                'product_id' => 'required|integer|exists:products,id',
                'qty' => 'required|integer|min:1',
            ]);
            Log::info('Validation passed');
            $stockCheck = \App\Models\StockCheckReq::create($validated);
            Log::info('Stock check saved');

            // Create notification for admin
            \App\Models\Notification::create([
                'type' => 'stock_check',
                // 'stock_check_id' => $stockCheck->id,
                'title' => 'New Stock Check Request',
                'message' => 'Stock check for product ID #' . $stockCheck->product_id . ' (Qty: ' . $stockCheck->qty . ') has been submitted.',
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Stock check request has been sent. You’ll get a response soon on your given number or email.",
                'data' => $stockCheck
            ], 201);
        } catch (\Exception $e) {
            Log::error('Stock check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Could not submit stock check. Try again later.'
            ], 500);
        }
    }
    
    // old function
    public function saveOrder(Request $request)
    {
    
        Log::info('Order Save Request:', $request->all());

        // dd($request->all());
        DB::beginTransaction();
        try {
            // 1. Save customer (or update if email/phone already exists)
            $customerData = $request->input('customer');
            // dd($customerData);
            $customer = Customer::updateOrCreate(
                [
                    'email' => $customerData['email'] ,
                    'phone' => $customerData['phone'] 
                ],
                [
                    'name' => $customerData['name'],
                    'address' => $customerData['address'] ?? null,
                    'device_info' => $customerData['device_info'] ?? null,
                    'status' => 'active'
                ]
            );

            // dd($customer->email);
            $setting = Setting::first();
            // 2. Save order
            $orderData = $request->input('order');
            $order = new Order();
            $order->customer_id = $customer->id;
            $order->order_number = $this->generateOrderNumber();
            $order->applied_discount = $setting->discount;
            $order->currency_symbol = $setting->currency_symbol;
            $order->order_type = $orderData['order_type'] ?? null;
            $order->tracking_id = Str::random(6);
            $order->total_amount = $orderData['total_amount'];
            $order->status = $orderData['status'] ?? 'pending';
            $order->delivery_address = $orderData['delivery_address'] ?? $customer->address;
            $order->special_instructions = $orderData['special_instructions'] ?? null;
            // pickup_time and table_number
            $order->table_number = $orderData['table_number'] ?? null;
            $order->pickup_time = $orderData['pickup_time'] ?? null;
            $order->save();

            if ($order->order_type === 'in_house') {

                $emails = User::Role(['manager', 'restaurant_owner', 'order_taker', 'kitchen', 'delivery_boy'])->pluck('email')->toArray();
                Mail::to($emails)->send(new NewOrderNotification($order));

                // dd($emails);
            }
            // 3. Save order details
            $orderDetails = $request->input('order_details', []);
            // dd($orderDetails);
            foreach ($orderDetails as $detail) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['total_price']
                ]);
            }

            // dd($order);
            // 4. Save notification for new order
           $notification = Notification::create([
            'type' => 'order-creation',
            'title' => 'Order Created!',
            'message' => 'A new order has been created!',
            'is_read' => false,
            // 'user_id' => $user->id, // Replace with the correct user variable
        ]);
// send to roles admin manager 
        $notification->users()->attach(
            User::role(['restaurant_owner', 'manager', 'kitchen'])->pluck('id')->toArray()
        );
        // dd($notification);

            DB::commit();




            // 6. Send emails safely
            try {
                // dd($customer->email);
                if ($customer->email) {
                    Mail::to($customer->email)->send(new OrderConfirmation($order));
                }

                $storeOwnerEmail = tenant()->owner_email;
                // dd($storeOwnerEmail);
                Mail::to($storeOwnerEmail)->send(new NewOrderNotification($order));

                // MailLogs::logMail(
                //     sendBy: Auth::user()->email ?? env('CONTACT_EMAIL'),
                //     sentTo: $customer->email,
                //     content: view('emails.order_placed_customer', [
                //         'order' => $order,
                //         'tenantDomain' => tenant()?->domains->first()?->domain ?? request()->getHost(),
                //     ])->render(),
                // );
                // // dd($data);
                // MailLogs::logMail(
                //     sendBy: Auth::user()->email ?? env('CONTACT_EMAIL'),
                //     sentTo: $storeOwnerEmail,
                //     content: view('emails.order_placed_owner', [
                //         'order' => $order,

                //     ])->render(),
                // );
            } catch (\Exception $mailEx) {
                Log::error('Mail sending failed: ' . $mailEx->getMessage());
                // don’t throw → order is still saved
                dd($mailEx);
            }


            return response()->json([
                'success' => true,
                'message' => 'Order saved successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
  

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Order Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save order: ' . $e->getMessage()
            ], 500);
        }
    }
        

    public function getNotifications(Request $request)
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->where('is_read', false)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    public function fetchCurrency()
    {
        $currencies = Currency::get();
        Log::info('currencies', [$currencies]);
        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }



    // check delivery adress ranges
    public function check(Request $request)
    {
        $request->validate([
            'restaurant_lat' => 'required|numeric',
            'restaurant_lng' => 'required|numeric',
            'user_lat' => 'required|numeric',
            'user_lng' => 'required|numeric',
        ]);

        $earthRadius = 6371; // km
        $dLat = deg2rad($request->user_lat - $request->restaurant_lat);
        $dLng = deg2rad($request->user_lng - $request->restaurant_lng);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($request->restaurant_lat)) * cos(deg2rad($request->user_lat)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        $maxDistance = 10; // Restaurant delivers within 10km radius

        return response()->json([
            'deliverable' => $distance <= $maxDistance,
            'distance_km' => round($distance, 2),
            'message' => $distance <= $maxDistance
                ? 'Delivery available to your area!'
                : 'Sorry, we do not deliver to your location.'
        ]);
    }
} 