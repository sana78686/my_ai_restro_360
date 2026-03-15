<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'creator', 'updater'])
                ->when($request->category_id, function ($q) use ($request) {
                    return $q->where('category_id', $request->category_id);
                })
                ->when($request->search, function ($q) use ($request) {
                    return $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('sku', 'like', '%' . $request->search . '%')
                        ->orWhere('barcode', 'like', '%' . $request->search . '%');
                })
                ->when($request->stock_status, function ($q) use ($request) {
                    if ($request->stock_status === 'low') {
                        return $q->whereRaw('stock_quantity <= min_stock_level');
                    } elseif ($request->stock_status === 'out') {
                        return $q->where('stock_quantity', 0);
                    }
                });

            $products = $query->orderBy('stock_quantity')
                ->paginate($request->per_page ?? 10);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock'
            ], 500);
        }
    }

    public function stockCheck(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer',
                'type' => 'required|in:in,out',
                'notes' => 'nullable|string'
            ]);

            $product = Product::findOrFail($validated['product_id']);

            // Create stock check record
            $stockCheck = StockCheck::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'type' => $validated['type'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id()
            ]);

            // Update product stock
            if ($validated['type'] === 'in') {
                $product->increment('stock_quantity', $validated['quantity']);
            } else {
                if ($product->stock_quantity < $validated['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }
                $product->decrement('stock_quantity', $validated['quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully',
                'data' => [
                    'stock_check' => $stockCheck,
                    'product' => $product->load(['category', 'creator', 'updater'])
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkStockCheck(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer',
                'items.*.type' => 'required|in:in,out',
                'items.*.notes' => 'nullable|string'
            ]);

            $stockChecks = [];
            $products = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create stock check record
                $stockCheck = StockCheck::create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'type' => $item['type'],
                    'notes' => $item['notes'] ?? null,
                    'created_by' => auth()->id()
                ]);

                // Update product stock
                if ($item['type'] === 'in') {
                    $product->increment('stock_quantity', $item['quantity']);
                } else {
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product: {$product->name}");
                    }
                    $product->decrement('stock_quantity', $item['quantity']);
                }

                $stockChecks[] = $stockCheck;
                $products[] = $product;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk stock update successful',
                'data' => [
                    'stock_checks' => $stockChecks,
                    'products' => $products
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating bulk stock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bulk stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stockHistory(Request $request)
    {
        try {
            $query = StockCheck::with(['product', 'creator'])
                ->when($request->product_id, function ($q) use ($request) {
                    return $q->where('product_id', $request->product_id);
                })
                ->when($request->type, function ($q) use ($request) {
                    return $q->where('type', $request->type);
                })
                ->when($request->date_from, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->date_from);
                })
                ->when($request->date_to, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->date_to);
                });

            $history = $query->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 10);

            return response()->json([
                'success' => true,
                'data' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stock history'
            ], 500);
        }
    }

    public function lowStockReport()
    {
        try {
            $lowStockProducts = Product::with(['category'])
                ->whereRaw('stock_quantity <= min_stock_level')
                ->orderBy('stock_quantity')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $lowStockProducts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching low stock report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch low stock report'
            ], 500);
        }
    }
} 