<?php

namespace App\Traits;

use App\Models\{
    Status,
};

trait Statusable
{
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
