<?php

namespace App\Http\Controllers\API\Dashboard;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use App\Models\Plan;
use App\Http\Controllers\Controller;


class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();

        return response()->json([
            'success' => true,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric',
            'currency' => 'nullable|string',
            'interval' => 'nullable|string|in:month,year',
             'features' => 'nullable|array',

        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $product = Product::create(['name' => $validated['name']]);

            $price = Price::create([
                'unit_amount' => $validated['price'] * 100,
                'currency' => $validated['currency'] ?? 'aud',
                'recurring' => ['interval' => $validated['interval'] ?? 'month'],
                'product' => $product->id,
            ]);

            $plan = Plan::create([
                'name' => $validated['name'],
                'price' => $validated['price'],
                'currency' => $validated['currency'] ?? 'aud',
                'interval' => $validated['interval'] ?? 'month',
                'stripe_product_id' => $product->id,
                'stripe_price_id' => $price->id,
                'features' => $validated['features'] ?? [],
            ]);

            return response()->json([
                'success' => true,
                'plan' => $plan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Update plan
    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'interval' => 'nullable|string|in:month,year',
             'features' => 'nullable|array',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Update Stripe Product name
            Product::update($plan->stripe_product_id, [
                'name' => $validated['name']
            ]);

            // Optionally create a new Stripe price if price changed
            if (isset($validated['price'])) {
                $price = Price::create([
                    'unit_amount' => $validated['price'] * 100,
                    'currency' => $validated['currency'] ?? ($plan->currency ?: 'aud'),
                    'recurring' => ['interval' => $validated['interval'] ?? $plan->interval],
                    'product' => $plan->stripe_product_id,
                ]);
                $plan->stripe_price_id = $price->id;
            }

            // Update local DB
            $plan->update([
                'name' => $validated['name'],
                'price' => $validated['price'] ?? $plan->price,
                'currency' => $validated['currency'] ?? ($plan->currency ?: 'aud'),
                'interval' => $validated['interval'] ?? $plan->interval,
                 'features' => $validated['features'] ?? $plan->features,
            ]);

            return response()->json([
                'success' => true,
                'plan' => $plan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ Delete plan
    public function destroy($id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return response()->json(['success' => false, 'message' => 'Plan not found'], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Delete Stripe Product (Price will be auto deleted)
            Product::update($plan->stripe_product_id, [
                'active' => false
            ]);

            // Delete local DB record
            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
