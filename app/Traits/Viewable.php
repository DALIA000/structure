<?php

namespace App\Traits;

use App\Models\View;

trait Viewable
{
    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function has_viewed()
    {
        $loggedinUser = app('loggedinUser');
        return $this->views()->where('user_id', $loggedinUser?->id);
    }
}
