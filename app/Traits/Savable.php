<?php

namespace App\Traits;

use App\Models\Save;

trait Savable
{
    // who saved this
    public function saved_self()
    {
        return $this->morphMany(Save::class, 'savable');
    }

    public function has_saved()
    {
        $loggedinUser = app('loggedinUser');
        return $this->saved_self()->where('user_id', $loggedinUser?->id);
    }
}
