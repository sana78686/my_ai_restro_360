<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
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

    public function store(Request $request)
    {
        try {
            Log::info('Request data: ' . json_encode($request->all()));
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'is_active' => 'boolean',
                'image' => 'nullable|string',
            ]);

            $created_by = auth()->id();
            $updated_by = auth()->id();

            // Automatically set sort_order to max+1 (or 0 if no categories exist)
            $sort_order = (Category::max('sort_order') ?? -1) + 1;

            $category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'image' => $validated['image'] ?? null,
                'sort_order' => $sort_order,
                'created_by' => $created_by,
                'updated_by' => $updated_by,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category->load(['parent', 'children'])
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category'
            ], 500);
        }
    }

    public function show(Category $category)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $category->load(['parent', 'children'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category'
            ], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'is_active' => 'boolean',
            ]);

            // Prevent setting a category as its own parent
            if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'A category cannot be its own parent'
                ], 400);
            }

            $validated['updated_by'] = auth()->id();

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->load(['parent', 'children'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category'
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            // Check if category has any subcategories or products
            if ($category->children()->exists() || $category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category as it has associated subcategories or products'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            
            // Check if category has any subcategories or products
            if ($category->children()->exists() || $category->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category as it has associated subcategories or products'
                ], 400);
            }

            $category->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Category permanently deleted'
            ]);
        } catch (\Exception $e) {
            Log::error('Error force deleting category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete category'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();

            return response()->json([
                'success' => true,
                'message' => 'Category restored successfully',
                'data' => $category->load(['parent', 'children'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore category'
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        foreach ($order as $item) {
            Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['success' => true, 'message' => 'Order updated']);
    }

    public function getPublicCategoriesWithProducts()
    {
        try {
            $categories = Category::with(['products' => function ($query) {
                $query->where('is_active', true);
            }])->where('is_active', true)->get(); // Also filter categories by is_active if applicable

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching public categories with products: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories and products'
            ], 500);
        }
    }
} 