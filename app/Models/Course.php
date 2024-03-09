<?php

namespace App\Models;

use App\Traits\Modelable;
use App\Traits\Statusable;
use App\Traits\Invoicable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
    use Statusable;
    use Modelable;
    use Invoicable;

    protected $guarded = [];

    protected $casts = [
        'status_id' => 'integer',
        'individual_price' => 'float',
        'group_discount' => 'float',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['sessions'];

    // prevent if any exists
    public static $cascade = ['subscribtions'];

    // locales columns
    public static $locales_columns = [
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function sessions()
    {
        return $this->hasMany(CourseSession::class);
    }

    public function upcomming_sessions()
    {
        return $this->hasMany(CourseSession::class)->where('status', 0)/* ->whereDate('date', '>=', Carbon::now()) */;
    }

    public function subscribtions()
    {
        return $this->hasMany(CourseSubscribtion::class);
    }

    public function getStageAttribute() :bool // stage
    {
        return (boolean) $this->hasMany(CourseSession::class)->where('status', 0)->count();
    }

    public function getAvailableSeatsAttribute() :int // available_seats
    {
        return $this->seats_count - $this->hasMany(CourseSubscribtion::class)->count();
    }

    public function getCanStartLiveAttribute() : bool // car_start_live
    {
        $session = $this->upcomming_sessions()->first();
        if(!$session){
            return false;
        }

        $now = Carbon::now();
        $session_datetime = Carbon::parse("$session?->date $session?->time");

        $session_starts_in = $session_datetime->diff($now);

        if(
            (
                (
                    $session_starts_in->y == 0 && 
                    $session_starts_in->m == 0 && 
                    $session_starts_in->d == 0 && 
                    $session_starts_in->h == 0 &&
                    $session_starts_in->i < 15 &&
                    $session_starts_in->invert == 1
                ) || (
                    $session_starts_in->y == 0 && 
                    $session_starts_in->m == 0 && 
                    $session_starts_in->d == 0 && 
                    $session_starts_in->h == 0 &&
                    $session_starts_in->i < 45 &&
                    $session_starts_in->invert == 0
                )
            ) && (
                $this->subscribtions()->count() > 0
            )
          ) {
            return true ;
        }

        return false;
    }

    public function has_subscribed()
    {
        $loggedinUser = app('loggedinUser');
        return $this->subscribtions()->where('user_id', $loggedinUser?->id);
    }
}
