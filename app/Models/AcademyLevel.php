<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class AcademyLevel extends Model
{
    use HasFactory, Localizable, CascadesDeletes;
    protected $cascadeDeletes = ['locales'];

    protected $guarded = [];

}
