<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator
{
  /*
   * Get a unique base path for the given media.
   */
  protected function getBasePath(Media $media): string
  {

    $directory = \Str::snake($media->collection_name). "/" . $media->model_id;

    return $directory;
  }

  /*
   * Get the path for conversions of the given media, relative to the root storage path.
   */
  public function getPathForResponsiveImages(Media $media): string
  {
    return $this->getBasePath($media) . '/thumbnails/';
  }
}