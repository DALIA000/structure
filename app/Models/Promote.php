<?php

namespace App\Models;

use App\Traits\Modelable;
use App\Traits\Statusable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Promote extends EloquentModel
{
    use HasFactory;
    use Modelable;
    use Statusable;
    use HasJsonRelationships;

    protected $guarded = [];

    protected $casts = [
        'promotable_id' => 'integer',
        'target' => 'json'
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

    public function promotable()
    {
        return $this->morphTo();
    }

    public function statistic()
    {
        return $this->hasOne(PromoteStatistic::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'promotable_id');
    }

    public function cities()
    {
        return $this->belongsToJson(City::class, 'target->cities', 'slug');
    }

    public function user_types()
    {
        return $this->belongsToJson(UserType::class, 'target->user_types');
    }
}
