<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class UserCompetitionOption extends EloquentModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'option_id' => 'integer',
    ];

    public function option()
    {
        return $this->belongsTo(CompetitionOption::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
