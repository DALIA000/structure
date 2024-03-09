<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class TrainerExperienceLevel extends Model
{
    use HasFactory;
    use Localizable;
    use CascadesDeletes;
    use SoftDeletes;
    
    protected $cascadeDeletes = ['locales'];

    protected $guarded = [];
}
