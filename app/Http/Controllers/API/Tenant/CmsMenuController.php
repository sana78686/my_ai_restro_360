<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\CMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CmsMenuController extends Controller
{
	public function index(Request $request)
	{
		try {
			$query = CMS::with(['menu', 'creator', 'updater']);

			// Apply filters
			if ($request->search) {
				$query->where(function($q) use ($request) {
					$q->where('page_title', 'like', '%' . $request->search . '%')
					  ->orWhere('keyword', 'like', '%' . $request->search . '%');
				});
			}

			if ($request->menu_id) {
				$query->where('menu_id', $request->menu_id);
			}

			if ($request->status) {
				$query->where('status', $request->status);
			}

			if ($request->only_deleted) {
				$query->onlyTrashed();
			}

			$cmsPages = $query->orderBy('order', 'asc')
							 ->orderBy('created_at', 'desc')
							 ->paginate($request->per_page ?? 10);

			return response()->json([
				'success' => true,
				'data' => $cmsPages
			]);
		} catch (\Exception $e) {
			Log::error('Error fetching CMS pages: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch CMS pages'
			], 500);
		}
	}

	public function store(Request $request)
	{
		try {
			$validated = $request->validate([
				'menu_id'   => 'nullable|exists:c_m_s,id',
				'page_title'=> 'required|string|max:255',
				'keyword'   => 'required|string|max:255',
				'content'   => 'nullable|string',
				'order'     => 'nullable',
				'status'    => 'required|in:active,inactive'
			]);

			// Coerce order to string if provided
			if (array_key_exists('order', $validated) && !is_null($validated['order'])) {
				$validated['order'] = (string) $validated['order'];
			}

			$validated['created_by'] = auth()->id();
			$validated['updated_by'] = auth()->id();

			$cmsPage = CMS::create($validated);

			return response()->json([
				'success' => true,
				'message' => 'CMS page created successfully',
				'data' => $cmsPage->load(['menu', 'creator', 'updater'])
			], 201);
		} catch (\Exception $e) {
			Log::error('Error creating CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to create CMS page'
			], 500);
		}
	}

	public function show($id)
	{
		try {
			$cmsPage = CMS::with(['menu', 'creator', 'updater'])->findOrFail($id);

			return response()->json([
				'success' => true,
				'data' => $cmsPage
			]);
		} catch (\Exception $e) {
			Log::error('Error fetching CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch CMS page'
			], 500);
		}
	}

	public function update(Request $request, $id)
	{
		try {
			$cmsPage = CMS::findOrFail($id);

			$validated = $request->validate([
				'menu_id'   => 'nullable|exists:c_m_s,id',
				'page_title'=> 'required|string|max:255',
				'keyword'   => 'required|string|max:255',
				'content'   => 'nullable|string',
				'order'     => 'nullable',
				'status'    => 'required|in:active,inactive'
			]);

			// Prevent setting itself as parent
			if (array_key_exists('menu_id', $validated) && $validated['menu_id'] && (int)$validated['menu_id'] === (int)$cmsPage->id) {
				return response()->json([
					'success' => false,
					'message' => 'A page cannot be set as its own parent.'
				], 422);
			}

			$validated['updated_by'] = auth()->id();
			if (array_key_exists('order', $validated) && !is_null($validated['order'])) {
				$validated['order'] = (string) $validated['order'];
			}

			$cmsPage->update($validated);

			return response()->json([
				'success' => true,
				'message' => 'CMS page updated successfully',
				'data' => $cmsPage->load(['menu', 'creator', 'updater'])
			]);
		} catch (\Exception $e) {
			Log::error('Error updating CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to update CMS page'
			], 500);
		}
	}

	public function destroy($id)
	{
		try {
			$cmsPage = CMS::findOrFail($id);
			$cmsPage->delete();

			return response()->json([
				'success' => true,
				'message' => 'CMS page deleted successfully'
			]);
		} catch (\Exception $e) {
			Log::error('Error deleting CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to delete CMS page'
			], 500);
		}
	}

	public function forceDelete($id)
	{
		try {
			$cmsPage = CMS::withTrashed()->findOrFail($id);
			$cmsPage->forceDelete();

			return response()->json([
				'success' => true,
				'message' => 'CMS page permanently deleted'
			]);
		} catch (\Exception $e) {
			Log::error('Error force deleting CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to permanently delete CMS page'
			], 500);
		}
	}

	public function restore($id)
	{
		try {
			$cmsPage = CMS::withTrashed()->findOrFail($id);
			$cmsPage->restore();

			return response()->json([
				'success' => true,
				'message' => 'CMS page restored successfully',
				'data' => $cmsPage->load(['menu', 'creator', 'updater'])
			]);
		} catch (\Exception $e) {
			Log::error('Error restoring CMS page: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to restore CMS page'
			], 500);
		}
	}

	public function reorder(Request $request)
	{
		try {
			$request->validate([
				'order' => 'required|array',
				'order.*.id' => 'required|exists:c_m_s,id',
				'order.*.order' => 'required'
			]);

			foreach ($request->order as $item) {
				CMS::where('id', $item['id'])->update(['order' => (string) $item['order']]);
			}

			return response()->json([
				'success' => true,
				'message' => 'Order updated successfully'
			]);
		} catch (\Exception $e) {
			Log::error('Error reordering CMS pages: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to update order'
			], 500);
		}
	}

	// Get selectable parent pages (top-level)
	public function getMenus(Request $request)
	{
		try {
			$excludeId = $request->query('exclude_id');
			$menus = CMS::active()
				->whereNull('menu_id')
				->when($excludeId, function ($q) use ($excludeId) {
					$q->where('id', '!=', $excludeId);
				})
				->orderBy('page_title')
				->get(['id', 'page_title as name']);

			return response()->json([
				'success' => true,
				'data' => $menus
			]);
		} catch (\Exception $e) {
			Log::error('Error fetching menus: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch menus'
			], 500);
		}
	}

	public function uploadImage(Request $request)
	{
		$request->validate([
			'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
		]);

		try {
			$imageName = time() . '.' . $request->image->extension();
			$request->image->move(public_path('uploads/cms_images'), $imageName);
			$imageUrl = asset('uploads/cms_images/' . $imageName);

			return response()->json(['url' => $imageUrl]);
		} catch (\Exception $e) {
			Log::error('Error uploading CMS image: ' . $e->getMessage());
			return response()->json(['message' => 'Image upload failed.'], 500);
		}
	}

	/**
	 * Get CMS page by slug (keyword)
	 */
	public function getPageBySlug($slug)
	{
		try {
			$page = CMS::with(['menu', 'creator', 'updater'])
				->where('keyword', $slug)
				->where('status', 'active')
				->first();

			if (!$page) {
				return response()->json([
					'success' => false,
					'message' => 'Page not found'
				], 404);
			}

			return response()->json([
				'success' => true,
				'data' => $page
			]);
		} catch (\Exception $e) {
			Log::error('Error fetching CMS page by slug: ' . $e->getMessage());
			return response()->json([
				'success' => false,
				'message' => 'Failed to fetch page'
			], 500);
		}
	}
}
