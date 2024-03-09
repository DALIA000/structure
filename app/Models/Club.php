<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Accountable;
use SoftDeletes;
use CascadesDeletes;
use App\Traits\MediaTrait;

class Club extends Model implements \Spatie\MediaLibrary\HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
    use MediaTrait;
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

    public function club_players()
    {
        return $this->hasMany(ClubPlayer::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function club_president()
    {
        return $this->hasOne(ClubPresident::class);
    }

    public function plans()
    {
        return $this->hasMany(ClubPlan::class);
    }

    public function competitions()
    {
        return $this->hasManyThrough(Competition::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function subscribers() // current_subscribtions
    {
        return $this->hasManyThrough(Subscribtion::class, ClubPlan::class, 'club_id', 'plan_id', 'id', 'id')->where('status', 1);
    }

    public function subscribtions() // all_subscribtions
    {
        return $this->hasManyThrough(Subscribtion::class, ClubPlan::class, 'club_id', 'plan_id', 'id', 'id');
    }

    public function club_achievments()
    {
        return $this->hasMany(ClubAchievment::class);
    }

    public function club_feature()
    {
        return $this->hasOne(ClubFeature::class);
    }

    public function club_members()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function latest_club_members()
    {
        return $this->hasOne(ClubMember::class)->orderBy('created_at', 'desc');
    }
}
