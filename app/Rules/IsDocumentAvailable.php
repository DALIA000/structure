<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Document;

class IsDocumentAvailable implements Rule
{
    public $imagable_type;
    public $imagable_id;
    public $slug;

    public function __construct($imagable_type=null, $imagable_id=null, $slug=false)
    {
        $this->imagable_type = $imagable_type;
        $this->imagable_id = $imagable_id;
        $this->slug = $slug;
    }

    public function passes($attribute, $value)
    {
        $id = null;
        if ($this->slug) {
            $id = $this->imagable_type::where('slug', $this->slug)?->first()?->id;
        }

        return Document::is_available($value, $this->imagable_type, $id ?: $this->imagable_id);
    }

    public function message()
    {
        return trans('messages.imageIsTaken');
    }
}
