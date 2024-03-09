<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    protected $casts = [
        'number' => 'integer',
    ];

    protected $guarded = [];

    public function subscribtion()
    {
        return $this->belongsTo(Subscribtion::class, 'user_id', 'user_id');
    }
}
