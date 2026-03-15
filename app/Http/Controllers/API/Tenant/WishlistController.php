<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function index()
    {
        try {
            $wishlists = Wishlist::with(['customer', 'product', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $wishlists
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching wishlists: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wishlists'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'product_id' => 'required|exists:products,id',
                'status' => 'required|string|in:active,inactive',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $wishlist = Wishlist::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item created successfully',
                'data' => $wishlist
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create wishlist item'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $wishlist = Wishlist::with(['customer', 'product', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $wishlist
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $wishlist = Wishlist::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'product_id' => 'required|exists:products,id',
                'status' => 'required|string|in:active,inactive',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $wishlist->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item updated successfully',
                'data' => $wishlist
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update wishlist item'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $wishlist = Wishlist::findOrFail($id);
            $wishlist->delete();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item soft deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error soft deleting wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to soft delete wishlist item'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $wishlist = Wishlist::withTrashed()->findOrFail($id);
            $wishlist->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item permanently deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error permanently deleting wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete wishlist item'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $wishlist = Wishlist::withTrashed()->findOrFail($id);
            $wishlist->restore();

            return response()->json([
                'success' => true,
                'message' => 'Wishlist item restored successfully',
                'data' => $wishlist
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring wishlist item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore wishlist item'
            ], 500);
        }
    }
} 