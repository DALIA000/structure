<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Accountable;
use App\Traits\MediaTrait;
use SoftDeletes;

class Trainer extends Model implements \Spatie\MediaLibrary\HasMedia
{
    use HasFactory;
    use CascadesDeletes;
    use Accountable;
    use MediaTrait;
    use SoftDeletes;

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

    public function trainer_experience_level()
    {
        return $this->belongsTo(TrainerExperienceLevel::class);
    }
}
