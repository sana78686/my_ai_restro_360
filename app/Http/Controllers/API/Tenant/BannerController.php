<?php

namespace App\Http\Controllers\API\Tenant;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Banner::query();

        if ($request->has('position')) {
            $query->position($request->position);
        }

        if ($request->boolean('active_only', false)) {
            $query->active();
        }

        $banners = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:50',
            'position' => 'nullable|string|in:hero,promo,deal',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $imagePath = FileUpload::store($request->file('image'), 'banners');

        $banner = Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image' => $imagePath,
            'cta_text' => $request->cta_text ?? 'Order Now',
            'cta_link' => $request->cta_link ?? '/menu',
            'price' => $request->price,
            'badge' => $request->badge,
            'position' => $request->position ?? 'hero',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully.',
            'data' => $banner,
        ], 201);
    }

    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $banner,
        ]);
    }

    public function update(Request $request, Banner $banner): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:50',
            'position' => 'nullable|string|in:hero,promo,deal',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($request->hasFile('image')) {
            FileUpload::deleteStored($banner->image);
            $banner->image = FileUpload::store($request->file('image'), 'banners');
        }

        $fillable = [
            'title', 'subtitle', 'description', 
            'cta_text', 'cta_link', 'price', 'badge',
            'position', 'sort_order', 'starts_at', 'ends_at'
        ];

        foreach ($fillable as $field) {
            if ($request->has($field)) {
                $banner->$field = $request->input($field);
            }
        }

        if ($request->has('is_active')) {
            $banner->is_active = $request->boolean('is_active');
        }

        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully.',
            'data' => $banner->fresh(),
        ]);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        FileUpload::deleteStored($banner->image);
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully.',
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:banners,id',
        ]);

        foreach ($request->order as $index => $bannerId) {
            Banner::where('id', $bannerId)->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banners reordered successfully.',
        ]);
    }

    public function toggle(Banner $banner): JsonResponse
    {
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return response()->json([
            'success' => true,
            'message' => 'Banner ' . ($banner->is_active ? 'activated' : 'deactivated') . ' successfully.',
            'data' => $banner,
        ]);
    }
}
