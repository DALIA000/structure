<?php

namespace App\Traits;

use App\Models\Report;

trait Reportable
{
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function has_reported()
    {
        $loggedinUser = app('loggedinUser');
        return $this->reports()->where('user_id', $loggedinUser?->id);
    }
}
