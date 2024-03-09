<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\{
    File,
    Setting,
};

class MediaRule implements Rule
{
    public function __construct(private $type = null)
    {
        $this->type = $type;
    }

    public function passes($attribute, $value)
    {
        
        $count = Media::where(function ($query) use ($value) {
            $query->where('id', $value);
            $model_id = request()->id;

            if ($this->type && $model_id) {
                $query->where('model_type', File::class)
                    ->orWhere(function ($query) use ($value, $model_id) {
                        $query->where(['id' => $value, 'model_id' => $model_id, 'model_type' => $this->type]);
                    });
            } else {
                $query->where('model_type', File::class);
            }
        })->count();

        return $count;
    }

    public function message()
    {
        if (!$this->type) {
            return 'the is media already assigned to another model.';
        } else {
            return 'this media is not belong to this model.';
        }
    }
}
