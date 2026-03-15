<!DOCTYPE html>
<html>
<head>
    {{-- {{ dd($role) }} --}}

    @php
        $role = $role ?? 'customer';
    @endphp
    <title>Order Status Updated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 30px; background-color: #fff; }
        .status-box { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 14px; }

        /* Status-specific colors */
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-processing { color: #17a2b8; font-weight: bold; }
        .status-preparing { color: #fd7e14; font-weight: bold; }
        .status-preparation-done { color: #007bff; font-weight: bold; }
        .status-completed { color: #28a745; font-weight: bold; }
        .status-cancelled { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Order Status Updated</h1>
        </div>

        <div class="content">
            {{-- Role-specific greetings --}}
        {{-- Role-specific greetings and content --}}
@switch($role)

    {{-- CUSTOMER --}}
    @case('customer')
        <p>Hello <strong>{{ $order->customer->name ?? 'Customer' }}</strong>,</p>

        @if($newStatus === 'cancelled')
            <p>We regret to inform you that your order
               <strong>#{{ $order->id }}</strong> has been
               <span class="status-cancelled">Cancelled</span>.
            </p>
        @elseif($newStatus === 'completed')
            <p>Good news! Your order
               <strong>#{{ $order->id }}</strong> has been
               <span class="status-completed">Completed</span>.
            </p>
        @else
            <p>Your order
               <strong>#{{ $order->id }}</strong> status is now
               <span class="status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>.
            </p>
        @endif
        @break


    {{-- STAFF (manager, kitchen, order_taker, delivery_boy) --}}
    @case('manager')
    @case('kitchen')
    @case('order_taker')
    @case('delivery_boy')
        <p>Hello <strong>{{ ucfirst($role) }}</strong>,</p>
        <p>Order <strong>#{{ $order->id }}</strong> has changed status to
            <span class="status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>.
        </p>
        <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
        @break


    {{-- OWNER --}}
    @case('owner')
        <p>Hello Owner,</p>
        <p>Order <strong>#{{ $order->id }}</strong> is now
            <span class="status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>.
        </p>
        @break


    {{-- FALLBACK --}}
    @default
        <p>Hello,</p>
        <p>Order <strong>#{{ $order->id }}</strong> status is now
            <span class="status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>.
        </p>
@endswitch


            {{-- Extra details for some statuses --}}
            @if($newStatus === 'preparing')
                <p><strong>Estimated Preparation Time:</strong> {{ $order->estimated_preparation_time ?? 'N/A' }}</p>
            @elseif($newStatus === 'preparation-done')
                <p><strong>Estimated Delivery Time:</strong> {{ $order->estimated_delivery_time ?? 'N/A' }}</p>
            @endif

            @if($order->tracking_id)
                <p><strong>Tracking Number:</strong> {{ $order->tracking_id }}</p>
            @endif
        </div>

        <div class="footer">
            <p>Thank you for using {{ \App\Helpers\AppNameHelper::getAppName() }}!</p>
            <p>© {{ date('Y') }} {{ \App\Helpers\AppNameHelper::getAppName() }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
