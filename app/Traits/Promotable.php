<?php

namespace App\Traits;

use App\Models\Promote;

trait Promotable
{
    public function promotes()
    {
        return $this->morphMany(Promote::class, 'promotable');
    }

    public function is_promoted() // is_promoted
    {
        return $this->promotes()->where('status', 1);
    }
}
