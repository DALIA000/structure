<?php

namespace App\Models;

use App\Traits\Modelable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Sound extends EloquentModel
{
    use HasFactory;
    use \CascadesDeletes;
    use Modelable;

    protected $guarded = ['id'];

    protected $casts = [
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
