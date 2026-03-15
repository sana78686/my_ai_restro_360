<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUpload
{
    /**
     * Upload single or multiple files to S3 under a tenant-aware folder.
     *
     * @param UploadedFile|array<int,UploadedFile> $files Single UploadedFile or an array of them
     * @param string $baseFolder Base folder under which files are stored (e.g., "logos", "products")
     * @param string|null $previous Existing path or array of paths to delete (for replacement)
     * @param bool $public Store publicly (uses storePublicly) when true
     * @return array{paths: array<int,string>, urls: array<int,string>} Stored relative paths and full URLs
     */
    public static function upload($files, string $baseFolder, $previous = null, bool $public = true): array
    {
        $tenantPrefix = 'tenant_' . tenant('id');
        $targetFolder = trim($tenantPrefix . '/' . trim($baseFolder, '/'), '/');

        $filesArray = is_array($files) ? array_values($files) : [$files];

        $storedPaths = [];
        $storedUrls = [];

        // dd($filesArray);
        foreach ($filesArray as $file) {
            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $path = $public
                ? $file->storePublicly($targetFolder, 's3')
                : $file->store($targetFolder, 's3');

            if ($path) {
                $storedPaths[] = $path;
                $storedUrls[] = Storage::disk('s3')->url($path);
            }
        }

        // Replace previous files if requested (delete after successful upload)
        if ($previous) {
            $previousList = Arr::wrap($previous);
            foreach ($previousList as $prevPath) {
                try {
                    if ($prevPath && Storage::disk('s3')->exists($prevPath)) {
                        Storage::disk('s3')->delete($prevPath);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to delete previous file from S3', [
                        'path' => $prevPath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // dd($storedPaths, $storedUrls);
        return [
            'paths' => $storedPaths,
            'urls' => $storedUrls,
        ];
    }
}


