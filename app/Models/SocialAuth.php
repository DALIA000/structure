<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class SocialAuth extends Model
{
    use CascadesDeletes;

    protected $table = 'social_auth';

    protected $cascadeDeletes = [];

    protected $guarded = [];

}
