<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class CompetitionOption extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'competition_id' => 'integer',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
