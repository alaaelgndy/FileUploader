<?php

namespace Elgndy\FileUploader\Traits;

use Elgndy\FileUploader\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

trait Uploadable
{
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function getMedia(...$types): Collection
    {
        $media = $this->media;

        if (count($types) > 0) {
            $media = $media->whereIn('file_type', $types);
        }
        return $media;
    }

    public function firstMedia(?string $type = null)
    {
        $media = $this->media;

        if ($type) {
            $media = $media->where('file_type', $type);
        }

        return $media->first();
    }

    public function mediaCount(...$types): int
    {
        return $this->getMedia(...$types)->count();
    }

    public function getImageAttribute()
    {
        $media = $this->firstMedia();

        if (!$media) {
            return '';
        }

        return Storage::url($media->file_path);
    }
}
