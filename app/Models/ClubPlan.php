<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class ClubPlan extends EloquentModel
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
        'club_plan_type_id' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = [];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'description'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function club_plan_type()
    {
        return $this->belongsTo(ClubPlanType::class);
    }

    public function subscribers () // current_subscribtions
    {
        return $this->hasMany(Subscribtion::class, 'plan_id')->where('status', 1);
    }

    public function subscribtions () // all_subscribtions
    {
        return $this->hasMany(Subscribtion::class, 'plan_id');
    }
}
