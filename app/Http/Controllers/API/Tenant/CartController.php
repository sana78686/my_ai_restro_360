<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        try {
            $carts = Cart::with(['customer', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $carts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching carts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch carts'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'status' => 'required|string|in:active,abandoned,completed',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $cart = Cart::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cart created successfully',
                'data' => $cart
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cart'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cart = Cart::with(['customer', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'status' => 'required|string|in:active,abandoned,completed',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $cart->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error soft deleting cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete cart'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $cart = Cart::withTrashed()->findOrFail($id);
            $cart->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Cart permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete cart'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $cart = Cart::withTrashed()->findOrFail($id);
            $cart->restore();

            return response()->json([
                'success' => true,
                'message' => 'Cart restored successfully',
                'data' => $cart
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore cart'
            ], 500);
        }
    }
} 