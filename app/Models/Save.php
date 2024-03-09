<?php

namespace App\Models;

use App\Traits\Modelable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use CascadesDeletes;

class Save extends EloquentModel
{
    use HasFactory;
    use CascadesDeletes;
    use Modelable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'savable_id' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savable()
    {
        return $this->morphTo();
    }
}
