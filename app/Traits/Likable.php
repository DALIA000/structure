<?php

namespace App\Traits;

use App\Models\Like;

trait Likable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likable');
    }

    public function has_liked()
    {
        $loggedinUser = app('loggedinUser');
        return $this->likes()->where('user_id', $loggedinUser?->id);
    }
}
