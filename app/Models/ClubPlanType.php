<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class ClubPlanType extends EloquentModel
{
    use HasFactory;
    use \CascadesDeletes;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['locales'];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'name'
    ];
}
