<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NormalizeMediaPaths extends Command
{
    protected $signature = 'media:normalize-paths {--dry-run}';

    protected $description = 'Normalize media s3_path custom property and settings.logo_path to use forward slashes and assets/ prefix';

    public function handle()
    {
        $dry = $this->option('dry-run');

        $this->info('Starting normalization of media paths' . ($dry ? ' (dry run)' : ''));

        $medias = Media::all();
        $this->info('Found ' . $medias->count() . ' media records');

        foreach ($medias as $media) {
            try {
                $s3Path = $media->getCustomProperty('s3_path');
                if ($s3Path) {
                    // normalize existing
                    $normalized = str_replace('\\', '/', $s3Path);
                    if ($normalized !== $s3Path) {
                        $this->line("Normalize media #{$media->id} s3_path: {$s3Path} -> {$normalized}");
                        if (! $dry) {
                            $media->setCustomProperty('s3_path', $normalized);
                            $media->save();
                        }
                    }
                    continue;
                }

                // derive from url if possible
                $url = $media->getUrl();
                $path = parse_url($url, PHP_URL_PATH) ?: '';
                $path = ltrim($path, '/');
                if (!$path) {
                    $this->warn("Could not derive path for media #{$media->id} (url: {$url})");
                    continue;
                }
                $normalized = str_replace('\\', '/', $path);
                // ensure assets/ prefix exists
                if (strpos($normalized, 'assets/') !== 0) {
                    $normalized = 'assets/' . $normalized;
                }

                $this->line("Set media #{$media->id} s3_path = {$normalized}");
                if (! $dry) {
                    $media->setCustomProperty('s3_path', $normalized);
                    $media->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to normalize media', ['id' => $media->id, 'error' => $e->getMessage()]);
            }
        }

        // Normalize settings.logo and logo_path
        $settings = Setting::all();
        foreach ($settings as $setting) {
            try {
                $logo = $setting->logo;
                if (!$logo) continue;
                // if logo already looks like a full CDN url with backslashes, normalize
                $parsed = parse_url($logo, PHP_URL_PATH) ?: '';
                $parsed = ltrim($parsed, '/');
                if (!$parsed) continue;
                $normalized = str_replace('\\', '/', $parsed);
                if (strpos($normalized, 'assets/') !== 0) {
                    $normalized = 'assets/' . $normalized;
                }
                $this->line("Setting #{$setting->id} logo_path -> {$normalized}");
                        if (! $dry) {
                            // store in logo_path if column exists
                            if (array_key_exists('logo_path', $setting->getAttributes()) || in_array('logo_path', $setting->getFillable())) {
                                $setting->logo_path = $normalized;
                                // update public logo using Storage disk url which respects disk configuration
                                try {
                                    $setting->logo = Storage::disk('s3')->url($normalized);
                                } catch (\Throwable $e) {
                                    // fallback: leave existing logo as-is
                                }
                                $setting->save();
                            }
                        }
            } catch (\Throwable $e) {
                Log::warning('Failed to normalize setting logo', ['id' => $setting->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info('Normalization complete');
        return 0;
    }
}
