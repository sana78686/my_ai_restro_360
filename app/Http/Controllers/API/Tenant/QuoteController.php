<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index()
    {
        try {
            $quotes = Quote::with(['items.product', 'customer', 'creator', 'updater'])
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'data' => $quotes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching quotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quotes'
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
                'notes' => 'nullable|string',
                'created_by' => 'nullable|exists:users,id',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            // Calculate total amount
            $totalAmount = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            // Create quote
            $quote = Quote::create([
                'customer_id' => $validated['customer_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => $validated['created_by'] ?? null,
                'updated_by' => $validated['updated_by'] ?? null
            ]);

            // Create quote items
            foreach ($items as $item) {
                $item['quote_id'] = $quote->id;
                QuoteItem::create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quote created successfully',
                'data' => $quote->load(['items.product', 'customer'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating quote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quote'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $quote = Quote::with(['items.product', 'customer', 'creator', 'updater'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $quote
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching quote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Quote not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $quote = Quote::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'status' => 'required|string|in:pending,approved,rejected',
                'notes' => 'nullable|string',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            // Calculate total amount
            $totalAmount = 0;
            $items = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            // Update quote
            $quote->update([
                'customer_id' => $validated['customer_id'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $validated['updated_by'] ?? null
            ]);

            // Delete existing items and create new ones
            $quote->items()->delete();
            foreach ($items as $item) {
                $item['quote_id'] = $quote->id;
                QuoteItem::create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quote updated successfully',
                'data' => $quote->load(['items.product', 'customer'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating quote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quote'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $quote = Quote::findOrFail($id);
            $quote->items()->delete();
            $quote->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quote deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting quote: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quote'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $quote = Quote::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|string|in:pending,approved,rejected',
                'notes' => 'nullable|string',
                'updated_by' => 'nullable|exists:users,id'
            ]);

            $quote->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'updated_by' => $validated['updated_by'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Quote status updated successfully',
                'data' => $quote->load(['items.product', 'customer'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating quote status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update quote status'
            ], 500);
        }
    }
} 