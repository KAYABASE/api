<?php

namespace App\Generators;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class MediaPathGenerator extends DefaultPathGenerator
{
    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        return $this->getModelDirectoryName($media->model_type) . DIRECTORY_SEPARATOR . $media->getKey();
    }

    /**
     * @param $className
     *
     * @return string
     */
    protected function getModelDirectoryName($className)
    {
        return Str::lower(Str::plural(class_basename($className)));
    }
}
