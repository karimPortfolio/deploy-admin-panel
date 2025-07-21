<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer as BaseDefaultFileNamer;

class DefaultFileNamer extends BaseDefaultFileNamer
{
  public function originalFileName(string $fileName): string
  {
    return \Str::random(32) . '-' . time();
  }
}