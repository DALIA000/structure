<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class ChangeUserTypeRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
    ];

    public function account()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->morphTo('user', 'user_type_class', 'id', 'user_id');
    }
}
