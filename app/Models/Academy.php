<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Accountable;
use SoftDeletes;

class Academy extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
    use Accountable;

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

    public function academy_level()
    {
        return $this->belongsTo(AcademyLevel::class);
    }

    public function players()
    {
        return $this->hasManyThrough(Player::class, AcademyPlayer::class, 'academy_id', 'id', 'id', 'player_id');
    }

    public function academy_president()
    {
        return $this->hasOne(AcademyPresident::class);
    }
}
