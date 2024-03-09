<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaRuleSetting implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

            $media = Media::where(function ($q) use ($value) {
                $q->where('model_type', "App\Models\Setting");
                $q->where('id', $value);
            })->first();


            if ($media) {
                return true;
            } else {
                return false;
            }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if (!$this->type) {
            return 'the is media already assigned to another model.';
        } else {
            return 'this media is not belong to this model.';
        }
    }
}
