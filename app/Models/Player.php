<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;
use CascadesDeletes;
use Accountable;

class Player extends Model
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
    protected $cascadeDeletes = [
        'academy_player'
    ];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
    ];

    public function academy_player()
    {
        return $this->hasOne(AcademyPlayer::class);
    }

    public function player_position()
    {
        return $this->belongsTo(PlayerPosition::class);
    }

    public function player_footness()
    {
        return $this->belongsTo(PlayerFootness::class);
    }
}
