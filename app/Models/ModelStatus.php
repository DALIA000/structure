<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModelStatus extends EloquentModel
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;

    protected $table = 'model_status';

    protected $guarded = [];

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

    public function model()
    {
        return $this->belongsTo(Model::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
