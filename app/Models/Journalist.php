<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Accountable;

class Journalist extends Model
{
    use HasFactory;
    use CascadesDeletes;
    use SoftDeletes;
    use Accountable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = [];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
    ];
}
