<?php

namespace App\Traits;

use App\Models\Comment;

trait Commentable
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function has_commented()
    {
        $loggedinUser = app('loggedinUser');
        return $this->comments()->where('user_id', $loggedinUser?->id);
    }
}
