<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('theme')->default('classic')->after('is_active');
            $table->string('tagline')->nullable()->after('business_name');
            $table->string('favicon')->nullable()->after('logo');
            
            // Hero section
            $table->string('hero_headline')->nullable();
            $table->string('hero_subheadline')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_cta_text')->nullable();
            $table->string('hero_cta_link')->nullable();
            
            // Colors
            $table->string('primary_color')->nullable()->default('#E31837');
            $table->string('secondary_color')->nullable()->default('#1A1A1A');
            $table->string('accent_color')->nullable()->default('#FFD700');
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('hide_from_search')->default(false);
            
            // Social links (if not exist)
            if (!Schema::hasColumn('settings', 'facebook_link')) {
                $table->string('facebook_link')->nullable();
            }
            if (!Schema::hasColumn('settings', 'instagram_link')) {
                $table->string('instagram_link')->nullable();
            }
            if (!Schema::hasColumn('settings', 'twitter_link')) {
                $table->string('twitter_link')->nullable();
            }
            $table->string('tiktok_link')->nullable();
            $table->string('youtube_link')->nullable();
            
            // Preferences
            $table->boolean('show_cookie_banner')->default(true);
            $table->boolean('maintenance_mode')->default(false);
            
            // Sections visibility
            $table->json('visible_sections')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'theme', 'tagline', 'favicon',
                'hero_headline', 'hero_subheadline', 'hero_image', 'hero_cta_text', 'hero_cta_link',
                'primary_color', 'secondary_color', 'accent_color',
                'meta_title', 'meta_description', 'meta_keywords', 'hide_from_search',
                'tiktok_link', 'youtube_link',
                'show_cookie_banner', 'maintenance_mode',
                'visible_sections'
            ]);
        });
    }
};
