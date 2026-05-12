<?php

namespace App\Helpers;

use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUpload
{
    /**
     * S3 keys must always use "/" (Flysystem/Laravel inject "\" on Windows).
     */
    private static function normalizeS3Key(string $keyOrRelativePath): string
    {
        return str_replace('\\', '/', $keyOrRelativePath);
    }

    /**
     * Stable stored name: airestro360-{tenantId}-{sanitized-original-basename}.{ext}
     *
     * @param  string|null  $tenantId  When null, uses current tenant context (if any).
     */
    public static function imageFileName(UploadedFile $file, ?string $tenantId = null): string
    {
        if ($tenantId === null) {
            $t = tenant('id');
            $tenantId = $t !== null ? (string) $t : 'unknown';
        }

        $original = basename((string) $file->getClientOriginalName());
        $base = pathinfo($original, PATHINFO_FILENAME);
        $ext = strtolower((string) ($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin'));
        $base = preg_replace('/[^a-zA-Z0-9._-]+/', '-', $base) ?? '';
        $base = trim($base, '-._');
        if ($base === '') {
            $base = 'file';
        }

        return 'airestro360-' . $tenantId . '-' . $base . '.' . $ext;
    }

    /**
     * Public URL for a stored path (S3 key or legacy public disk path), or full URLs unchanged.
     */
    public static function urlForStored(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        } catch (\Throwable $e) {
        }

        try {
            $s3Path = self::normalizeS3Key($path);

            return Storage::disk('s3')->url($s3Path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Remove a file from S3 or legacy public disk (skips remote http values).
     */
    public static function deleteStored(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }
        // Legacy uploads used local `public` disk (e.g. profile-photos/...). Try that first
        // so we do not call S3 HeadObject with a key that only exists locally.
        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);

                return;
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to delete file from public disk', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
        try {
            $s3Path = self::normalizeS3Key($path);
            if (Storage::disk('s3')->exists($s3Path)) {
                Storage::disk('s3')->delete($s3Path);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to delete file from S3', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Write to S3 using PutObject without an ACL.
     *
     * Laravel's Flysystem S3 adapter always sends an ACL. Buckets with "Bucket owner enforced"
     * (ACLs disabled) reject PutObject requests that include ACL — uploads silently fail when
     * the disk uses throw:false.
     */
    private static function putUploadedFileWithoutS3Acl(UploadedFile $file, string $relativePath): bool
    {
        $disk = Storage::disk('s3');

        if (! ($disk instanceof AwsS3V3Adapter)) {
            Log::warning('filesystems.disks.s3 is not Laravel AwsS3V3Adapter; ACL-free PutObject skipped');

            return false;
        }

        $bucket = config('filesystems.disks.s3.bucket');
        if (! $bucket) {
            Log::error('S3 bucket is not configured (AWS_BUCKET).');

            return false;
        }

        $normalizedRelative = self::normalizeS3Key(trim($relativePath, '/'));

        $realPath = $file->getRealPath();
        if (! $realPath || ! is_readable($realPath)) {
            Log::error('Cannot read uploaded file for S3', ['relativePath' => $normalizedRelative]);

            return false;
        }

        $stream = fopen($realPath, 'rb');
        if ($stream === false) {
            return false;
        }

        /** @see normalizeS3Key() — Laravel's path() joins with "\" on Windows; S3 requires "/". */
        $key = self::normalizeS3Key($disk->path($normalizedRelative));
        $mime = $file->getClientMimeType() ?: (function_exists('mime_content_type') ? @mime_content_type($realPath) : null);

        try {
            $params = [
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => $stream,
            ];
            if ($mime) {
                $params['ContentType'] = $mime;
            }

            $disk->getClient()->putObject($params);
        } catch (\Throwable $e) {
            Log::error('S3 PutObject failed', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return false;
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return true;
    }

    /**
     * Upload single or multiple files to S3 under a tenant-aware folder.
     *
     * @param  UploadedFile|array<int,UploadedFile>  $files  Single UploadedFile or an array of them
     * @param  string|null  $previous  Existing path or array of paths to delete (for replacement)
     * @param  string|null  $tenantIdOverride  Central / landlord uploads: pass tenant id when tenant() is not set
     * @return array{paths: array<int,string>, urls: array<int,string>}
     */
    public static function upload($files, string $baseFolder, $previous = null, bool $public = true, ?string $tenantIdOverride = null): array
    {
        $tid = $tenantIdOverride;
        if ($tid === null) {
            $t = tenant('id');
            $tid = $t !== null ? (string) $t : null;
        }
        $tenantKey = $tid ?? 'unknown';

        $tenantPrefix = 'tenant_' . $tenantKey;
        $targetFolder = trim($tenantPrefix . '/' . trim($baseFolder, '/'), '/');

        $filesArray = is_array($files) ? array_values($files) : [$files];

        $storedPaths = [];
        $storedUrls = [];

        foreach ($filesArray as $file) {
            if (! ($file instanceof UploadedFile)) {
                continue;
            }

            $fileName = self::imageFileName($file, $tenantKey);
            $relativePath = self::normalizeS3Key(trim($targetFolder . '/' . $fileName, '/'));

            if (! self::putUploadedFileWithoutS3Acl($file, $relativePath)) {
                Log::error('S3 upload failed — see preceding PutObject/Aws log entry.', [
                    'relativePath' => $relativePath,
                ]);

                continue;
            }

            $storedPaths[] = $relativePath;
            $storedUrls[] = Storage::disk('s3')->url($relativePath);
        }

        if ($previous && $storedPaths !== []) {
            $previousList = Arr::wrap($previous);
            foreach ($previousList as $prevPath) {
                self::deleteStored($prevPath);
            }
        }

        return [
            'paths' => $storedPaths,
            'urls' => $storedUrls,
        ];
    }
}
