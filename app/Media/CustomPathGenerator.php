<?php

namespace App\Media;

use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomPathGenerator implements PathGenerator
{
  public function getPath(Media $media): string
  {
    // Store files under tenant-specific folder + collection name
    $tenantPrefix = tenant('id') ? 'tenant_' . tenant('id') : 'global';

    return $tenantPrefix . '/' . $media->collection_name . '/';
  }

  public function getPathForConversions(Media $media): string
  {
    return $this->getPath($media) . 'conversions/';
  }

  public function getPathForResponsiveImages(Media $media): string
  {
    return $this->getPath($media) . 'responsive/';
  }
}
