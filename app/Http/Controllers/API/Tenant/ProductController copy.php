<?php

namespace App\Http\Controllers\API\Tenant;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\FileUpload;
use Illuminate\Support\Str;
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

            // add media urls for each product in the pagination data
            $products->getCollection()->transform(function ($product) {
                $product->images = $product->getMedia('images')->map(function ($m) {
                    $fileName = $m->file_name ?? basename($m->getPath() ?? '');
                    $s3Path = $m->getCustomProperty('s3_path');
                    if (!$s3Path) {
                        $s3Path = 'assets/tenant_' . tenant('id') . '/images/' . $fileName;
                    }
                    $s3Path = str_replace('\\', '/', $s3Path);

                    // prefer stored public_url custom property (set at upload time)
                    $publicUrl = $m->getCustomProperty('public_url') ?? null;
                    if (!$publicUrl) {
                        try {
                            $publicUrl = Storage::disk('s3')->url($s3Path);
                        } catch (\Throwable $e) {
                            // last resort use media library url
                            try { 
                                $publicUrl = $m->getUrl(); 
                            } catch (\Throwable $_) { 
                                $publicUrl = null; 
                            }
                        }
                    }

                    return ['id' => $m->id, 'url' => $publicUrl, 'path' => $s3Path];
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

    public function store(Request $request)
    {
        try {
            Log::info('Store product request: ' . json_encode($request->all()));
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
            Log::info('Validated data: ' . json_encode($validated));

            // Handle image upload first if present
            $attachedMedia = [];
            $uploadResult = null;
            
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $uploadResult = FileUpload::upload($files, 'products', null, true);
                Log::info('File upload result:', $uploadResult);
            }

            // Automatically set sort_order to max+1 (or 0 if no products exist)
            $sort_order = (Product::max('sort_order') ?? -1) + 1;

            $created_by = auth()->id();
            $updated_by = auth()->id();

            // Create product first
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'image' => null, // Will be set after media attachment
                'is_active' => $validated['status'] === 'active',
                'is_featured' => $validated['is_featured'] ?? false,
                'stock_quantity' => $validated['stock_quantity'] ?? 0,
                'sku' => $validated['sku'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'cost_price' => $validated['cost_price'] ?? 0,
                'min_stock_level' => $validated['min_stock_level'] ?? 0,
                'status' => $validated['status'] ?? 'active',
                'sort_order' => $sort_order,
                'created_by' => $created_by,
                'updated_by' => $updated_by,
            ]);

            // If images were uploaded, attach them to media library after product is persisted
            if ($uploadResult && !empty($uploadResult['paths'])) {
                $paths = $uploadResult['paths'];
                $urls  = $uploadResult['urls'];
                
                foreach ($paths as $idx => $path) {
                    $normalized = str_replace('\\', '/', $path);
                    try {
                        $media = $product->addMediaFromDisk($normalized, 's3')
                            ->usingFileName(basename($normalized))
                            ->toMediaCollection('images', 's3');

                        $publicUrl = $urls[$idx] ?? Storage::disk('s3')->url($normalized);
                        
                        $media->setCustomProperty('s3_path', $normalized);
                        $media->setCustomProperty('public_url', $publicUrl);
                        $media->save();

                        $attachedMedia[] = [
                            'id' => $media->id, 
                            'url' => $publicUrl, 
                            'path' => $normalized
                        ];
                        
                        Log::info("Media attached successfully:", [
                            'media_id' => $media->id,
                            'path' => $normalized,
                            'url' => $publicUrl
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Product media attach failed: ' . $e->getMessage(), ['path' => $normalized]);
                    }
                }

                // Set main image path for backward compatibility (first image)
                if (!empty($attachedMedia)) {
                    $first = $attachedMedia[0];
                    $product->image = $first['url'] ?? $first['path'] ?? null;
                    $product->save();
                }
            }

            // Load images for response
            $product->load(['category', 'creator', 'updater']);
            $product = $this->appendImagesToProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        try {
            $product->load(['category', 'creator', 'updater']);
            $product = $this->appendImagesToProduct($product);

            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching product: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product'
            ], 500);
        }
    }

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
                'is_featured' => 'nullable|boolean',
            ]);
            Log::info('Validated data: ' . json_encode($validated));

            // Handle removal of existing media items (ids passed from frontend)
            if ($request->has('remove_media_ids')) {
                $ids = (array) $request->input('remove_media_ids', []);
                foreach ($ids as $mid) {
                    try {
                        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($mid);
                        if ($media && $media->model_type == Product::class && $media->model_id == $product->id) {
                            $media->delete();
                        }
                    } catch (\Throwable $e) {
                        Log::warning('Failed to delete media id ' . $mid . ': ' . $e->getMessage());
                    }
                }
            }

            // Handle newly uploaded images
            $attachedMedia = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $uploadResult = FileUpload::upload($files, 'products', null, true);
                Log::info('File upload result:', $uploadResult);

                if (!empty($uploadResult['paths'])) {
                    $paths = $uploadResult['paths'];
                    $urls  = $uploadResult['urls'];
                    
                    foreach ($paths as $idx => $path) {
                        $normalized = str_replace('\\', '/', $path);
                        try {
                            $media = $product->addMediaFromDisk($normalized, 's3')
                                ->usingFileName(basename($normalized))
                                ->toMediaCollection('images', 's3');

                            $publicUrl = $urls[$idx] ?? Storage::disk('s3')->url($normalized);
                            
                            $media->setCustomProperty('s3_path', $normalized);
                            $media->setCustomProperty('public_url', $publicUrl);
                            $media->save();

                            $attachedMedia[] = [
                                'id' => $media->id, 
                                'url' => $publicUrl, 
                                'path' => $normalized
                            ];
                            
                            Log::info("Media attached successfully:", [
                                'media_id' => $media->id,
                                'path' => $normalized,
                                'url' => $publicUrl
                            ]);
                        } catch (\Throwable $e) {
                            Log::error('Product media attach failed: ' . $e->getMessage(), ['path' => $normalized]);
                        }
                    }

                    // Update backward-compatible image field with first image path/url
                    if (!empty($attachedMedia)) {
                        $first = $attachedMedia[0];
                        $validated['image'] = $first['url'] ?? $first['path'] ?? null;
                    }
                }
            }

            // Update is_active based on status
            if (isset($validated['status'])) {
                $validated['is_active'] = $validated['status'] === 'active';
            }

            $validated['updated_by'] = auth()->id();

            $product->update($validated);

            // Load images for response
            $product->load(['category', 'creator', 'updater']);
            $product = $this->appendImagesToProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
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
            Log::info('Destroying product: ' . $product->id);
            // Check if product has any orders or stock checks
            // if ($product->orderItems()->exists() || $product->stockChecks()->exists()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Cannot delete product as it has associated records'
            //     ], 400);
            // }

            // remove media from collection
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
            // remove media
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

            // Load with images for response
            $product->load(['category', 'creator', 'updater']);
            $product = $this->appendImagesToProduct($product);

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

    /**
     * Append images to product response consistently
     */
    private function appendImagesToProduct(Product $product): Product
    {
        $product->images = $product->getMedia('images')->map(function($media) {
            // Use stored custom properties first
            $s3Path = $media->getCustomProperty('s3_path');
            $publicUrl = $media->getCustomProperty('public_url');
            
            // If custom properties are missing, try to generate them
            if (!$s3Path || !$publicUrl) {
                $fileName = $media->file_name ?? basename($media->getPath() ?? '');
                
                if (!$s3Path) {
                    $s3Path = 'tenant_' . tenant('id') . '/products/' . $fileName;
                }
                
                if (!$publicUrl) {
                    try {
                        $publicUrl = Storage::disk('s3')->url($s3Path);
                    } catch (\Throwable $e) {
                        try {
                            $publicUrl = $media->getUrl();
                        } catch (\Throwable $_) {
                            $publicUrl = null;
                        }
                    }
                }
            }

            // Normalize path
            $s3Path = str_replace('\\', '/', $s3Path);

            return [
                'id' => $media->id,
                'url' => $publicUrl,
                'path' => $s3Path
            ];
        })->toArray();

        return $product;
    }
}