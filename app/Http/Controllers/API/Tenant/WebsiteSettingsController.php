<?php

namespace App\Http\Controllers\API\Tenant;

use App\Helpers\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebsiteSettingsController extends Controller
{
    protected array $availableThemes = [
        'classic' => [
            'id' => 'classic',
            'name' => 'classic',
            'description' => 'Traditional, warm design with classic restaurant feel',
            'preview_color' => '#ffc107',
        ],
        'modern' => [
            'id' => 'modern',
            'name' => 'modern',
            'description' => 'Bold, dark theme with contemporary aesthetics',
            'preview_color' => '#1e293b',
        ],
        'minimal' => [
            'id' => 'minimal',
            'name' => 'minimal',
            'description' => 'Clean, whitespace-focused minimalist design',
            'preview_color' => '#eceff1',
        ],
        'blaze' => [
            'id' => 'blaze',
            'name' => 'blaze',
            'description' => 'Bold fast-food theme with sticky cart and category tabs',
            'preview_color' => '#E31837',
        ],
    ];

    public function getThemes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => array_values($this->availableThemes),
        ]);
    }

    public function getWebsiteSettings(): JsonResponse
    {
        $settings = Setting::first();

        if (!$settings) {
            return response()->json([
                'success' => true,
                'data' => $this->getDefaults(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'theme' => $settings->theme ?? 'classic',
                'business_name' => $settings->business_name,
                'tagline' => $settings->tagline,
                'description' => $settings->description,
                'logo' => $settings->logo,
                'logo_url' => $settings->logo_url ?? null,
                'favicon' => $settings->favicon,
                'favicon_url' => $this->getFileUrl($settings->favicon),
                
                'hero_headline' => $settings->hero_headline,
                'hero_subheadline' => $settings->hero_subheadline,
                'hero_image' => $settings->hero_image,
                'hero_image_url' => $this->getFileUrl($settings->hero_image),
                'hero_cta_text' => $settings->hero_cta_text ?? 'Order Now',
                'hero_cta_link' => $settings->hero_cta_link ?? '/menu',
                
                'primary_color' => $settings->primary_color ?? '#E31837',
                'secondary_color' => $settings->secondary_color ?? '#1A1A1A',
                'accent_color' => $settings->accent_color ?? '#FFD700',
                
                'meta_title' => $settings->meta_title,
                'meta_description' => $settings->meta_description,
                'meta_keywords' => $settings->meta_keywords,
                'hide_from_search' => (bool) ($settings->hide_from_search ?? false),
                
                'facebook_link' => $settings->facebook_link,
                'instagram_link' => $settings->instagram_link,
                'twitter_link' => $settings->twitter_link,
                'tiktok_link' => $settings->tiktok_link,
                'youtube_link' => $settings->youtube_link,
                
                'public_phone' => $settings->public_phone,
                'public_email' => $settings->public_email,
                'address' => $settings->address,
                
                'show_cookie_banner' => (bool) ($settings->show_cookie_banner ?? true),
                'maintenance_mode' => (bool) ($settings->maintenance_mode ?? false),
                
                'visible_sections' => $settings->visible_sections ?? [
                    'hero' => true,
                    'categories' => true,
                    'best_sellers' => true,
                    'deals' => true,
                    'promotions' => true,
                    'gallery' => false,
                    'testimonials' => false,
                ],
                
                'currency_symbol' => $settings->currency_symbol ?? '$',
            ],
        ]);
    }

    public function updateWebsiteSettings(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'nullable|string|in:' . implode(',', array_keys($this->availableThemes)),
            'tagline' => 'nullable|string|max:255',
            'hero_headline' => 'nullable|string|max:255',
            'hero_subheadline' => 'nullable|string|max:500',
            'hero_cta_text' => 'nullable|string|max:50',
            'hero_cta_link' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'accent_color' => 'nullable|string|max:20',
            'meta_title' => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'hide_from_search' => 'nullable|boolean',
            'tiktok_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'show_cookie_banner' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
            'visible_sections' => 'nullable|array',
        ]);

        $settings = Setting::firstOrCreate([]);

        $fillable = [
            'theme', 'tagline',
            'hero_headline', 'hero_subheadline', 'hero_cta_text', 'hero_cta_link',
            'primary_color', 'secondary_color', 'accent_color',
            'meta_title', 'meta_description', 'meta_keywords', 'hide_from_search',
            'tiktok_link', 'youtube_link',
            'show_cookie_banner', 'maintenance_mode', 'visible_sections',
        ];

        foreach ($fillable as $field) {
            if ($request->has($field)) {
                $settings->$field = $request->input($field);
            }
        }

        $settings->save();

        // Sync theme to central tenants table so tenant('theme') works
        if ($request->has('theme')) {
            try {
                $tenant = tenant();
                if ($tenant) {
                    $tenant->theme = $request->input('theme');
                    $tenant->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to sync theme to central tenant', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Website settings updated successfully.',
        ]);
    }

    public function uploadHeroImage(Request $request): JsonResponse
    {
        $request->validate([
            'hero_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $settings = Setting::firstOrCreate([]);

        if ($settings->hero_image) {
            FileUpload::deleteStored($settings->hero_image);
        }

        $path = FileUpload::store($request->file('hero_image'), 'banners');
        $settings->hero_image = $path;
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Hero image uploaded successfully.',
            'data' => [
                'hero_image' => $path,
                'hero_image_url' => $this->getFileUrl($path),
            ],
        ]);
    }

    public function uploadFavicon(Request $request): JsonResponse
    {
        $request->validate([
            'favicon' => 'required|image|mimes:png,ico|max:512',
        ]);

        $settings = Setting::firstOrCreate([]);

        if ($settings->favicon) {
            FileUpload::deleteStored($settings->favicon);
        }

        $path = FileUpload::store($request->file('favicon'), 'branding');
        $settings->favicon = $path;
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Favicon uploaded successfully.',
            'data' => [
                'favicon' => $path,
                'favicon_url' => $this->getFileUrl($path),
            ],
        ]);
    }

    protected function getFileUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        if (config('filesystems.default') === 's3') {
            return \Illuminate\Support\Facades\Storage::disk('s3')->url($path);
        }

        return asset('storage/' . $path);
    }

    protected function getDefaults(): array
    {
        return [
            'theme' => 'classic',
            'business_name' => null,
            'tagline' => null,
            'description' => null,
            'logo' => null,
            'logo_url' => null,
            'favicon' => null,
            'favicon_url' => null,
            'hero_headline' => 'Welcome to Our Restaurant',
            'hero_subheadline' => 'Fresh food, made with love',
            'hero_image' => null,
            'hero_image_url' => null,
            'hero_cta_text' => 'Order Now',
            'hero_cta_link' => '/menu',
            'primary_color' => '#E31837',
            'secondary_color' => '#1A1A1A',
            'accent_color' => '#FFD700',
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'hide_from_search' => false,
            'facebook_link' => null,
            'instagram_link' => null,
            'twitter_link' => null,
            'tiktok_link' => null,
            'youtube_link' => null,
            'public_phone' => null,
            'public_email' => null,
            'address' => null,
            'show_cookie_banner' => true,
            'maintenance_mode' => false,
            'visible_sections' => [
                'hero' => true,
                'categories' => true,
                'best_sellers' => true,
                'deals' => true,
                'promotions' => true,
                'gallery' => false,
                'testimonials' => false,
            ],
            'currency_symbol' => '$',
        ];
    }
}
