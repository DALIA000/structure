<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory, Localizable;

    protected $cascadeDeletes = ['locales'];

    protected $fillable = [
        'slug',
    ];
}
