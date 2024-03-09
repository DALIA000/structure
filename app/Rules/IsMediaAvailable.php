<?php

namespace App\Rules;

use App\Traits\MediaTrait;
use Illuminate\Contracts\Validation\Rule;

class IsMediaAvailable implements Rule
{
    public $mediable_type;
    public $mediable_id;
    public $slug;

    public function __construct($mediable_type=null, $mediable_id=null, $slug=false)
    {
        $this->mediable_type = $mediable_type;
        $this->mediable_id = $mediable_id;
        $this->slug = $slug;
    }

    public function passes($attribute, $value)
    {
        $id = null;
        if ($this->slug) {
            $id = $this->mediable_type::where('slug', $this->slug)?->first()?->id;
        }

        return MediaTrait::is_available($value, $this->mediable_type, $id ?: $this->mediable_id);
    }

    public function message()
    {
        return trans('messages.imageIsTaken');
    }
}
