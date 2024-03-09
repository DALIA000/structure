<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Reportable;
use App\Traits\Savable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\MediaTrait;
use App\Traits\Modelable;
use Musonza\Chat\Traits\Messageable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use CascadesDeletes;
    use MediaTrait;
    use Modelable;
    use Messageable;
    use Savable;
    // use Reportable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
      'email_verified_at' => 'datetime',
      'keywords' => 'array'
    ];

    // related files
    public static $files = ['image'];

    // cascade delete
    protected $cascadeDeletes = [
        'verification_codes',
        'social_auth',
        'user_preferences',
        'certifications',
        'competitions',
        'user_competition_options',
        'saves',
        'blocks',
        'blocked',
        'follows',
        'followers',
        'videos',
        'video_tags',
        'reports',
        'likes',
        'comments',
        'active_subscribtions',
        'participation'
    ];

    // prevent if any exists
    public static $cascade = [];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function verification_code()
    {
        return $this->hasOne(VerificationCode::class);
    }

    public function verification_codes()
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function social_auth()
    {
        return $this->hasOne(SocialAuth::class);
    }

    public function user_type()
    {
        return $this->belongsTo(UserType::class, 'user_type_class', 'user_type');
    }

    public function getResourceUserTypeAttribute() // resource_user_type
    {
        return $this->user?->status_id !== 1 ? UserType::find(1): $this->user_type;
    }

    public function user()
    {
        return $this->morphTo('user', 'user_type_class', 'id', 'user_id');
    }

    public function user_type_requests()
    {
        return $this->hasMany(ChangeUserTypeRequest::class);
    }

    public function user_preferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function preferences()
    {
        return $this->hasManyThrough(Preference::class, UserPreference::class, 'user_id', 'id', 'id', 'preference_id');
    }

    public function certifications()
    {
        return $this->hasMany(UserCertification::class);
    }

    public function certification()
    {
        return $this->hasOne(UserCertification::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // academy
    public function players()
    {
        return $this->hasManyThrough(User::class, AcademyPlayer::class, 'academy_account_id', 'id', 'id', 'player_account_id');
    }

    // club
    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }

    // 
    public function user_competition_options()
    {
        return $this->hasMany(UserCompetitionOption::class);
    }

    // lists
    public function saves()
    {
        return $this->hasMany(Save::class, 'user_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'user_id');
    }

    public function blocked()
    {
        return $this->hasMany(Block::class, 'blockable_id')->where('blockable_type', SELF::class);
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function followings()
    {
        return $this->hasMany(Follow::class, 'user_id')->where('is_pending', 0);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'followable_id')->where('followable_type', SELF::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, Video::class);
    }

    public function video_tags()
    {
        return $this->hasMany(VideoTag::class);
    }

    public function tagged_videos()
    {
        return $this->hasManyThrough(Video::class, VideoTag::class, 'user_id', 'id', 'id', 'video_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function getAccountIsPublicAttribute() // account_is_public
    {

        $account_preference = $this->hasMany(UserPreference::class)->where(function ($query) {
            $query->whereHas('preference', function ($query) {
                $query->where('slug', 'account');
            });
        })->first();
        return $account_preference ? (int) $account_preference?->value : 1;
        // return $this->hasManyThrough(Preference::class, UserPreference::class)->where('slug', 'account')->first();
    }

    public function has_followed()
    {
        $loggedinUser = app('loggedinUser');
        return $this->followers()->where('user_id', $loggedinUser?->id)->where('is_pending', 0);
    }

    public function has_sent_follow_request()
    {
        $loggedinUser = app('loggedinUser');
        return $this->followers()->where('user_id', $loggedinUser?->id)->where('is_pending', 1);
    }

    public function getHasActivePlanAttribute() // has_active_plan
    {
        return $this->active_subscribtions()->count();
    }

    public function subscribtions()
    {
        return $this->hasMany(Subscribtion::class);
    }

    public function suspends()
    {
        return $this->hasMany(Suspend::class);
    }

    public function active_subscribtions()
    {
        return $this->hasMany(Subscribtion::class)->where('status', 1);
    }

    public function active_subscribtion()
    {
        return $this->hasOne(Subscribtion::class)->where('status', 1)->with(['plan', 'plan.club']);
    }

    public function getIsMessagableAttribute() { // is_messagable
        $loggedinUser = app('loggedinUser');

        return (bool) User::where('id', $this->id)->where(function ($query) use ($loggedinUser) {
            $query->where(function ($query) use ($loggedinUser) {
                // followings where private
                $query->whereIn('id', $loggedinUser?->followings?->pluck('followable_id')?->unique()->toArray())
                    ->where(function ($query) use ($loggedinUser) {
                        $query->whereHas('preferences', function ($query) use ($loggedinUser) {
                            $query->where('slug', 'account')
                                ->where('value', 0);
                        });
                    });
                })

                // public accounts
                ->orWhere(function ($query) use ($loggedinUser) {
                    $query->whereHas('preferences', function ($query) use ($loggedinUser) {
                        $query->where('slug', 'account')
                            ->where('value', 1);
                    });
                })

                // profissional accounts
                ->orWhere(function ($query) use ($loggedinUser) {
                    $query->whereDoesntHave('preferences', function ($query) use ($loggedinUser) {
                        $query->where('slug', 'account');
                    });
                })

                // private account where already has conversation with loggedin user
                ->orWhere(function ($query) use ($loggedinUser) {
                    $query->whereHas('preferences', function ($query) use ($loggedinUser) {
                        $query->where('slug', 'account')
                            ->where('value', 0);
                    })->whereHas('participation.conversation.participants', function ($query) use ($loggedinUser) {
                        $query->where('messageable_id', $loggedinUser?->id);
                    });
                });
            })->whereNot(function ($query) use ($loggedinUser) {
                $query->whereHas('blocks', function ($query) use ($loggedinUser) {
                    $query->where('blockable_id', $loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($loggedinUser) {
                    $query->where('user_id', $loggedinUser?->id);
                });
            })->count();
    }

    public function getFullNameAttribute() { // full_name
        return $this->user?->business_name ?: "{$this->user?->first_name} {$this->user?->last_name}";
    } 
}
