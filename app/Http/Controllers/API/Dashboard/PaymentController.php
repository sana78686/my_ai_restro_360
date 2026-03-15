<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments with filters.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['tenant', 'plan', 'subscription']);

        // Filter by tenant
        if ($request->has('tenant_id') && $request->tenant_id) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->has('payment_type') && $request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by plan
        if ($request->has('plan_id') && $request->plan_id) {
            $query->where('plan_id', $request->plan_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by tenant name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('owner_email', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            })->orWhere('email', 'like', "%{$search}%")
              ->orWhere('stripe_payment_intent_id', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $payments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = Payment::with(['tenant', 'plan', 'subscription'])->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Get payment statistics.
     */
    public function getStats(Request $request)
    {
        $query = Payment::query();

        // Apply date filter if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Total revenue (only succeeded payments)
        $totalRevenue = (clone $query)->where('status', 'succeeded')->sum('amount');

        // This month's revenue
        $thisMonthRevenue = (clone $query)
            ->where('status', 'succeeded')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // Last month's revenue
        $lastMonthRevenue = (clone $query)
            ->where('status', 'succeeded')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('amount');

        // Pending payments count
        $pendingPayments = (clone $query)->where('status', 'pending')->count();

        // Failed payments count
        $failedPayments = (clone $query)->where('status', 'failed')->count();

        // Successful payments count
        $successfulPayments = (clone $query)->where('status', 'succeeded')->count();

        // Total payments count
        $totalPayments = (clone $query)->count();

        // Revenue by payment type
        $revenueByType = (clone $query)
            ->where('status', 'succeeded')
            ->select('payment_type', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_type')
            ->get()
            ->pluck('total', 'payment_type');

        // Monthly revenue trend (last 12 months)
        $monthlyTrend = (clone $query)
            ->where('status', 'succeeded')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month, 1)->format('Y-m'),
                    'total' => $item->total
                ];
            });

        // Revenue by plan
        $revenueByPlan = (clone $query)
            ->where('status', 'succeeded')
            ->join('plans', 'payments.plan_id', '=', 'plans.id')
            ->select('plans.name', 'plans.id', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('plans.id', 'plans.name')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'this_month_revenue' => $thisMonthRevenue,
                'last_month_revenue' => $lastMonthRevenue,
                'pending_payments' => $pendingPayments,
                'failed_payments' => $failedPayments,
                'successful_payments' => $successfulPayments,
                'total_payments' => $totalPayments,
                'revenue_by_type' => $revenueByType,
                'monthly_trend' => $monthlyTrend,
                'revenue_by_plan' => $revenueByPlan,
            ]
        ]);
    }
}
