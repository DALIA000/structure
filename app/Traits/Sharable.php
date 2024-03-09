<?php

namespace App\Traits;

use App\Models\Share;

trait Sharable
{
    public function shares()
    {
        return $this->morphMany(Share::class, 'sharable');
    }

    public function has_shared()
    {
        $loggedinUser = app('loggedinUser');
        return $this->shares()->where('user_id', $loggedinUser?->id);
    }
}
