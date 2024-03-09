<?php

namespace App\Models;

use App\Traits\Likable;
use App\Traits\Modelable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use CascadesDeletes;

class Like extends EloquentModel
{
    use HasFactory;
    use CascadesDeletes;
    use Modelable;
    use Likable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'likable_id' => 'integer',
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

    public function likable() 
    {
        return $this->morphTo();
    } 

    public function model()
    {
        return $this->belongsTo(Model::class, 'likable_type', 'type');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
