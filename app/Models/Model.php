<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends EloquentModel
{
    use HasFactory;
    use Localizable;
    use SoftDeletes;
    use CascadesDeletes;

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
        'name',
    ];

    public function status_options()
    {
        return $this->hasManyThrough(Status::class, ModelStatus::class, 'model_id', 'id', 'id', 'status_id');
    }
}
