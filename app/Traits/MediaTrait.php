<?php

namespace App\Traits;

use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\File;

trait MediaTrait
{
    use InteractsWithMedia;

    public function getFilesAttribute()
    {
        $media = $this->getMedia('media');
        if (!$media->isEmpty()) {
            return $media;
        } else {
            return null;
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaConversion('media')
            ->format(Manipulations::FORMAT_WEBP)
            ->nonQueued();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('cover')
            ->nonQueued();
    }

    public static function is_available($id, $ignore_type, $ignore_id)
    {
      $count = Media::where(function ($query) use ($id, $ignore_type, $ignore_id)
      {
        $query->where('id', $id);

        if ($ignore_type && $ignore_id) {
          $query->where('model_id', false)
                ->orWhere(function ($query) use ($id, $ignore_type, $ignore_id)
                {
                  $query->where(['id' => $id, 'model_id' => $ignore_id, 'model_type' => $ignore_type]);
                })->orWhere(function ($query) use ($id, $ignore_type, $ignore_id)
                {
                  $query->where(['id' => $id, 'model_type' => File::class]);
                });
        }else{
          $query->where('model_id', null);
        }
      })->count();

      return $count;
    }
}
