<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'creator', 'updater'])
                ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
                ->when($request->search, fn($q) => $q->where('name', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%'));

            if ($request->has('only_deleted') && $request->only_deleted) {
                $query = $query->onlyTrashed();
            }
            if ($request->status) {
                $query = $query->where('status', $request->status);
            }

            $products = $query->orderBy('sort_order')->paginate($request->per_page ?? 10);

            $products->getCollection()->transform(function ($product) {
                $product->images = collect($product->getMedia('images'))->map(function ($m) {
                    $fileName = $m->file_name ?? basename($m->getPath() ?? '');
                    $s3Path = $m->getCustomProperty('s3_path') ?? ('assets/tenant_' . tenant('id') . '/images/' . $fileName);
                    $s3Path = str_replace('\\', '/', $s3Path);

                    $publicUrl = $m->getCustomProperty('public_url') ?? null;
                    if (!$publicUrl) {
                        try { $publicUrl = Storage::disk('s3')->url($s3Path); } catch (\Throwable $e) {
                            try { $publicUrl = $m->getUrl(); } catch (\Throwable $_) { $publicUrl = null; }
                        }
                    }

                    return ['id' => $m->id, 'url' => $publicUrl, 'path' => $s3Path];
                })->toArray();

                return $product;
            });

            return response()->json(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch products'], 500);
        }
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'nullable|boolean',
        ]);

        // Upload images first
        $uploadResult = [];
        $firstImageUrl = null;

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $product_id = $request->product_id;
            $uploadResult = FileUpload::upload($files, 'products/product_id', null, true);

            // FIX: Replace backslashes with forward slashes in URLs
            if ($uploadResult && !empty($uploadResult['urls'])) {
                $uploadResult['urls'] = array_map(function($url) {
                    // Replace all backslashes with forward slashes
                    return str_replace('\\', '/', $url);
                }, $uploadResult['urls']);

                $firstImageUrl = $uploadResult['urls'][0];
                // dd('First image URL after fix:', $firstImageUrl);
            }

            // Also fix paths if they contain backslashes
            if ($uploadResult && !empty($uploadResult['paths'])) {
                $uploadResult['paths'] = array_map(function($path) {
                    return str_replace('\\', '/', $path);
                }, $uploadResult['paths']);
            }
        }

        // Debug: Check the fixed URLs
        \Log::info('Fixed upload result:', [
            'paths' => $uploadResult['paths'] ?? [],
            'urls' => $uploadResult['urls'] ?? []
        ]);

        // Create product
        $sort_order = (Product::max('sort_order') ?? -1) + 1;

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'image' => $firstImageUrl,
            'is_active' => $validated['status'] === 'active',
            'is_featured' => $validated['is_featured'] ?? false,
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'sku' => $validated['sku'] ?? null,
            'barcode' => $validated['barcode'] ?? null,
            'cost_price' => $validated['cost_price'] ?? 0,
            'min_stock_level' => $validated['min_stock_level'] ?? 0,
            'status' => $validated['status'] ?? 'active',
            'sort_order' => $sort_order,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Store exact S3 paths in media library
        if ($uploadResult && !empty($uploadResult['paths'])) {
            foreach ($uploadResult['paths'] as $idx => $s3Path) {
                try {
                    $publicUrl = $uploadResult['urls'][$idx] ?? null;

                    $media = $product->media()->create([
                        'file_name' => basename($s3Path),
                        'name' => 'image_' . ($idx + 1),
                        'mime_type' => $this->getMimeTypeFromPath($s3Path),
                        'disk' => 's3',
                        'conversions_disk' => 's3',
                        'collection_name' => 'images',
                        'size' => 0,
                        'manipulations' => [],
                        'custom_properties' => [
                            's3_path' => $s3Path,
                            'public_url' => $publicUrl, // This should now be fixed
                            'original_name' => $request->file('images')[$idx]->getClientOriginalName() ?? null
                        ],
                        'generated_conversions' => [],
                        'responsive_images' => [],
                    ]);

                    \Log::info('Media stored with URL:', [
                        'media_id' => $media->id,
                        'public_url' => $publicUrl,
                        's3_path' => $s3Path
                    ]);

                } catch (\Throwable $e) {
                    Log::error('Media attach failed: ' . $e->getMessage(), [
                        'path' => $s3Path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Load images for response
        $product->load(['category', 'creator', 'updater', 'media']);

        // Format response to include images
        $responseData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'image' => $product->image, // Single image for backward compatibility
            'is_active' => $product->is_active,
            'is_featured' => $product->is_featured,
            'stock_quantity' => $product->stock_quantity,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'cost_price' => $product->cost_price,
            'min_stock_level' => $product->min_stock_level,
            'status' => $product->status,
            'sort_order' => $product->sort_order,
            'category' => $product->category,
            'created_by' => $product->creator,
            'updated_by' => $product->updater,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'images' => $product->getMedia('images')->map(function($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getCustomProperty('public_url'),
                    's3_path' => $media->getCustomProperty('s3_path'),
                    'file_name' => $media->file_name,
                    'original_name' => $media->getCustomProperty('original_name')
                ];
            })->toArray()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $responseData
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error creating product: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create product: ' . $e->getMessage()
        ], 500);
    }
}



// Helper function to get mime type
private function getMimeTypeFromPath($path)
{
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
    ];

    return $mimeTypes[strtolower($extension)] ?? 'image/jpeg';
}
    public function show(Product $product)
    {
        try {
            $product->load(['category', 'creator', 'updater']);
            $product->images = collect($product->getMedia('images'))->map(function($m) {
                $fileName = $m->file_name ?? basename($m->getPath() ?? '');
                $s3Path = $m->getCustomProperty('s3_path') ?? ('assets/tenant_' . tenant('id') . '/images/' . $fileName);
                $s3Path = str_replace('\\', '/', $s3Path);
                $publicUrl = $m->getCustomProperty('public_url') ?? null;
                try { if (!$publicUrl) $publicUrl = $m->getUrl(); } catch (\Throwable $e) {}
                return ['id' => $m->id, 'url' => $publicUrl, 'path' => $s3Path];
            })->toArray();

            return response()->json(['success' => true, 'data' => $product]);
        } catch (\Exception $e) {
            Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to fetch product'], 500);
        }
    }

//     public function update(Request $request, Product $product)
// {
//     try {
//         $validated = $request->validate([
//             'name' => 'sometimes|required|string|max:255',
//             'description' => 'nullable|string',
//             'category_id' => 'sometimes|required|exists:categories,id',
//             'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
//             'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
//             'price' => 'sometimes|required|numeric|min:0',
//             'cost_price' => 'sometimes|required|numeric|min:0',
//             'stock_quantity' => 'sometimes|required|integer|min:0',
//             'min_stock_level' => 'sometimes|required|integer|min:0',
//             'status' => 'sometimes|required|in:active,inactive',
//             'is_active' => 'sometimes|boolean',
//             'is_featured' => 'sometimes|boolean',
//         ]);

//         // Handle media deletion
//         if ($request->has('remove_media_ids')) {
//             $ids = (array) $request->input('remove_media_ids', []);
//             foreach ($ids as $mid) {
//                 try {
//                     $m = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mid);
//                     if ($m && $m->model_type == Product::class && $m->model_id == $product->id) {
//                         $m->delete();
//                     }
//                 } catch (\Throwable $e) {
//                     Log::warning('Failed to delete media id ' . $mid . ': ' . $e->getMessage());
//                 }
//             }
//         }

//         // Handle new image uploads
//         $uploadResult = [];
//         $firstImageUrl = null;

//         if ($request->hasFile('images')) {
//             $files = $request->file('images');
//             $uploadResult = FileUpload::upload($files, 'products', null, true);

//             // Normalize URLs and paths (same as store method)
//             if ($uploadResult && !empty($uploadResult['urls'])) {
//                 $uploadResult['urls'] = array_map(function($url) {
//                     return str_replace('\\', '/', $url);
//                 }, $uploadResult['urls']);

//                 $firstImageUrl = $uploadResult['urls'][0];
//             }

//             if ($uploadResult && !empty($uploadResult['paths'])) {
//                 $uploadResult['paths'] = array_map(function($path) {
//                     return str_replace('\\', '/', $path);
//                 }, $uploadResult['paths']);
//             }

//             // Store new images in media library (consistent with store method)
//             if (!empty($uploadResult['paths'])) {
//                 foreach ($uploadResult['paths'] as $idx => $s3Path) {
//                     try {
//                         $publicUrl = $uploadResult['urls'][$idx] ?? null;
//                         $originalName = $request->file('images')[$idx]->getClientOriginalName() ?? null;

//                         $media = $product->media()->create([
//                             'file_name' => basename($s3Path),
//                             'name' => 'image_' . ($product->media()->count() + $idx + 1),
//                             'mime_type' => $this->getMimeTypeFromPath($s3Path),
//                             'disk' => 's3',
//                             'conversions_disk' => 's3',
//                             'collection_name' => 'images',
//                             'size' => 0,
//                             'manipulations' => [],
//                             'custom_properties' => [
//                                 's3_path' => $s3Path,
//                                 'public_url' => $publicUrl,
//                                 'original_name' => $originalName
//                             ],
//                             'generated_conversions' => [],
//                             'responsive_images' => [],
//                         ]);

//                         \Log::info('Media updated with URL:', [
//                             'media_id' => $media->id,
//                             'public_url' => $publicUrl,
//                             's3_path' => $s3Path
//                         ]);

//                     } catch (\Throwable $e) {
//                         Log::error('Media attach failed in update: ' . $e->getMessage(), [
//                             'path' => $s3Path,
//                             'error' => $e->getMessage()
//                         ]);
//                     }
//                 }

//                 // Update main image if we have new images and no existing main image
//                 if ($firstImageUrl && empty($product->image)) {
//                     $validated['image'] = $firstImageUrl;
//                 }
//             }
//         }

//         // Handle status to is_active conversion
//         if (isset($validated['status'])) {
//             $validated['is_active'] = $validated['status'] === 'active';
//         }

//         $validated['updated_by'] = auth()->id();
//         $product->update($validated);

//         // Load relationships and format response
//         $product->load(['category', 'creator', 'updater', 'media']);

//         $responseData = [
//             'id' => $product->id,
//             'name' => $product->name,
//             'description' => $product->description,
//             'price' => $product->price,
//             'category_id' => $product->category_id,
//             'image' => $product->image,
//             'is_active' => $product->is_active,
//             'is_featured' => $product->is_featured,
//             'stock_quantity' => $product->stock_quantity,
//             'sku' => $product->sku,
//             'barcode' => $product->barcode,
//             'cost_price' => $product->cost_price,
//             'min_stock_level' => $product->min_stock_level,
//             'status' => $product->status,
//             'sort_order' => $product->sort_order,
//             'category' => $product->category,
//             'created_by' => $product->creator,
//             'updated_by' => $product->updater,
//             'created_at' => $product->created_at,
//             'updated_at' => $product->updated_at,
//             'images' => $product->getMedia('images')->map(function($media) {
//                 return [
//                     'id' => $media->id,
//                     'url' => $media->getCustomProperty('public_url') ?? $media->getUrl(),
//                     's3_path' => $media->getCustomProperty('s3_path'),
//                     'file_name' => $media->file_name,
//                     'original_name' => $media->getCustomProperty('original_name')
//                 ];
//             })->toArray()
//         ];

//         return response()->json([
//             'success' => true,
//             'message' => 'Product updated successfully',
//             'data' => $responseData
//         ]);

//     } catch (\Exception $e) {
//         Log::error('Error updating product: ' . $e->getMessage());
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to update product: ' . $e->getMessage()
//         ], 500);
//     }
// }

public function update(Request $request, Product $product)
{
    try {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'min_stock_level' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:active,inactive',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        // Store current main image for comparison
        $currentMainImage = $product->image;

        // Handle media deletion FIRST
        $removedMainImage = false;
        if ($request->has('remove_media_ids')) {
            $ids = (array) $request->input('remove_media_ids', []);

            foreach ($ids as $mid) {
                try {
                    $m = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mid);
                    if ($m && $m->model_type == Product::class && $m->model_id == $product->id) {
                        // Check if we're removing the image that matches the current main image
                        $mediaUrl = $m->getCustomProperty('public_url') ?? $m->getUrl();
                        if ($mediaUrl === $currentMainImage) {
                            $removedMainImage = true;
                        }
                        $m->delete();
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to delete media id ' . $mid . ': ' . $e->getMessage());
                }
            }
        }

        // Handle new image uploads
        $uploadResult = [];
        $firstImageUrl = null;

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $uploadResult = FileUpload::upload($files, 'products', null, true);

            // Normalize URLs and paths (same as store method)
            if ($uploadResult && !empty($uploadResult['urls'])) {
                $uploadResult['urls'] = array_map(function($url) {
                    return str_replace('\\', '/', $url);
                }, $uploadResult['urls']);

                $firstImageUrl = $uploadResult['urls'][0];
            }

            if ($uploadResult && !empty($uploadResult['paths'])) {
                $uploadResult['paths'] = array_map(function($path) {
                    return str_replace('\\', '/', $path);
                }, $uploadResult['paths']);
            }

            // Store new images in media library (consistent with store method)
            if (!empty($uploadResult['paths'])) {
                foreach ($uploadResult['paths'] as $idx => $s3Path) {
                    try {
                        $publicUrl = $uploadResult['urls'][$idx] ?? null;
                        $originalName = $request->file('images')[$idx]->getClientOriginalName() ?? null;

                        $media = $product->media()->create([
                            'file_name' => basename($s3Path),
                            'name' => 'image_' . ($product->media()->count() + $idx + 1),
                            'mime_type' => $this->getMimeTypeFromPath($s3Path),
                            'disk' => 's3',
                            'conversions_disk' => 's3',
                            'collection_name' => 'images',
                            'size' => 0,
                            'manipulations' => [],
                            'custom_properties' => [
                                's3_path' => $s3Path,
                                'public_url' => $publicUrl,
                                'original_name' => $originalName
                            ],
                            'generated_conversions' => [],
                            'responsive_images' => [],
                        ]);

                        \Log::info('Media updated with URL:', [
                            'media_id' => $media->id,
                            'public_url' => $publicUrl,
                            's3_path' => $s3Path
                        ]);

                    } catch (\Throwable $e) {
                        Log::error('Media attach failed in update: ' . $e->getMessage(), [
                            'path' => $s3Path,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        // UPDATE MAIN IMAGE LOGIC
        $remainingMedia = $product->getMedia('images');

        if ($remainingMedia->isNotEmpty()) {
            // If we have remaining images, use the first one as main image
            $firstMedia = $remainingMedia->first();
            $newMainImage = $firstMedia->getCustomProperty('public_url') ?? $firstMedia->getUrl();
            $validated['image'] = $newMainImage;
        } else if ($firstImageUrl) {
            // If no remaining images but we uploaded new ones, use first new image
            $validated['image'] = $firstImageUrl;
        } else {
            // If no images left at all, set to null
            $validated['image'] = null;
        }

        // COMPLETE FIELD HANDLING - This is what was missing
        // Handle status to is_active conversion (only if status is provided)
        if (isset($validated['status'])) {
            $validated['is_active'] = $validated['status'] === 'active';
        } else {
            // If status is not provided but is_active is, maintain the relationship
            if (isset($validated['is_active'])) {
                $validated['status'] = $validated['is_active'] ? 'active' : 'inactive';
            }
            // If neither is provided, keep existing values
        }

        // Handle is_featured if not provided
        if (!isset($validated['is_featured'])) {
            // If not provided in request, keep the existing value
            $validated['is_featured'] = $product->is_featured;
        }

        // Handle is_active if not provided (and status also not provided)
        if (!isset($validated['is_active']) && !isset($validated['status'])) {
            // If neither is provided, keep the existing value
            $validated['is_active'] = $product->is_active;
        }

        // Ensure all required fields have values for partial updates
        $fieldsToEnsure = [
            'name', 'description', 'category_id', 'price', 'cost_price',
            'stock_quantity', 'min_stock_level', 'sku', 'barcode'
        ];

        foreach ($fieldsToEnsure as $field) {
            if (!isset($validated[$field])) {
                // If field not in request, use existing value from product
                $validated[$field] = $product->$field;
            }
        }

        $validated['updated_by'] = auth()->id();

        // Log the update for debugging
        \Log::info('Updating product with data:', [
            'product_id' => $product->id,
            'update_data' => $validated
        ]);

        $product->update($validated);

        // Load relationships and format response
        $product->load(['category', 'creator', 'updater', 'media']);

        $responseData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'image' => $product->image,
            'is_active' => $product->is_active,
            'is_featured' => $product->is_featured,
            'stock_quantity' => $product->stock_quantity,
            'sku' => $product->sku,
            'barcode' => $product->barcode,
            'cost_price' => $product->cost_price,
            'min_stock_level' => $product->min_stock_level,
            'status' => $product->status,
            'sort_order' => $product->sort_order,
            'category' => $product->category,
            'created_by' => $product->creator,
            'updated_by' => $product->updater,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
            'images' => $product->getMedia('images')->map(function($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getCustomProperty('public_url') ?? $media->getUrl(),
                    's3_path' => $media->getCustomProperty('s3_path'),
                    'file_name' => $media->file_name,
                    'original_name' => $media->getCustomProperty('original_name')
                ];
            })->toArray()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $responseData
        ]);

    } catch (\Exception $e) {
        Log::error('Error updating product: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update product: ' . $e->getMessage()
        ], 500);
    }
}

    public function destroy(Product $product)
    {
        try {
            $product->clearMediaCollection('images');
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product'
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->clearMediaCollection('images');
            $product->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Product permanently deleted'
            ]);
        } catch (\Exception $e) {
            Log::error('Error force deleting product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete product'
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();

            $product->load(['category', 'creator', 'updater']);
            $product->images = $product->getMedia('images')->map(function($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getCustomProperty('public_url') ?? $media->getUrl(),
                    'path' => $media->getCustomProperty('s3_path')
                ];
            })->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Product restored successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error restoring product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore product'
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order', []);
        foreach ($order as $item) {
            Product::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return response()->json(['success' => true, 'message' => 'Order updated']);
    }



}
