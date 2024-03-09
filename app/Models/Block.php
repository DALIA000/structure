<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Block extends EloquentModel
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'blockable_id' => 'integer',
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

    public function blockable() 
    {
        return $this->morphTo();
    } 
}
