<?php

namespace App\Models;

use App\Traits\Commentable;
use App\Traits\MediaTrait;
use App\Traits\Modelable;
use App\Traits\Reportable;
use App\Traits\Viewable;
use App\Traits\Sharable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spatie\MediaLibrary\HasMedia;

class Competition extends EloquentModel implements HasMedia
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;
    use Commentable;
    use Viewable;
    use Sharable;
    use Modelable;
    use MediaTrait;
    use Reportable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'winners_count' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->hasOneThrough(Club::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function subscribtions()
    {
        return $this->hasMany(CompetitionSubscribtion::class);
    }

    public function subscribers()
    {
        return $this->hasManyThrough(User::class, CompetitionSubscribtion::class, 'competition_id', 'id', 'id', 'user_id');
    }

    public function winners()
    {
        return $this->hasManyThrough(User::class, CompetitionSubscribtion::class, 'competition_id', 'id', 'id', 'user_id')->where('status', 1);
    }

    public function has_subscribed()
    {
        $loggedinUser = app('loggedinUser');
        return $this->subscribtions()->where('user_id', $loggedinUser?->id);
    }

    /* public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    } */

    public function options() 
    {
        return $this->hasMany(CompetitionOption::class);
    }

    public function right_option() 
    {
        return $this->hasOne(CompetitionOption::class)->where('is_right_option', 1);
    }

    public function getEndsAtAttribute() // ends_at
    {
        $now = Carbon::now();
        $ends_at = Carbon::parse($this->created_at)->addDays($this->days);
        
        $diff = $ends_at->diff($now);
        return $diff->invert ? $diff : null;
    }

    public function participations() 
    {
        return $this->hasManyThrough(UserCompetitionOption::class, CompetitionOption::class, 'competition_id', 'option_id', 'id', 'id');
    }

    public function has_participated() 
    {
        $loggedinUser = app('loggedinUser');
        return $this->participations()->where(['user_id' => $loggedinUser?->id]);
    }

    public function status() : Attribute
    {
        return Attribute::make(
            get: fn () => ($this->winners->count() < $this->winners_count) && ($this->ends_at?->d || $this->ends_at?->h)
        );
    }
}
