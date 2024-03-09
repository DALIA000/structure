<?php

namespace App\Models;

use App\Models\Like;
use App\Traits\Commentable;
use App\Traits\Likable;
use App\Traits\Localizable;
use App\Traits\Modelable;
use App\Traits\Promotable;
use App\Traits\Reportable;
use App\Traits\Savable;
use App\Traits\Viewable;
use App\Traits\Sharable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\MediaTrait;

class Video extends EloquentModel implements HasMedia
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;
    use Likable;
    use Viewable;
    use Sharable;
    use Savable;
    use Modelable;
    use MediaTrait;
    use Commentable;
    use Reportable;
    use Promotable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'commentable_id' => 'integer',
        'model_id' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['course'];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
    ];


    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(club::class);
    }

    public function course()
    {
        return $this->hasOne(Course::class);
    }

    public function getIsCourseAttribute() // is_course
    {
        return (bool) $this->course()->count();
    }

    public function video_tages()
    {
        return $this->hasMany(VideoTag::class);
    }

    public function tagged_users()
    {
        return $this->hasManyThrough(User::class, VideoTag::class, 'video_id', 'id', 'id', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function comments_status()
    {
        return $this->belongsTo(Status::class, 'comments_status_id');
    }

    public function getCoverAttribute () // cover
    {
        return $this->files ? $this->files[0]?->getUrl('cover') : File::find(2)?->media?->first()?->original_url;
    }

    public function getVideoAttribute () // video
    {
        return $this->files ? $this->files[0]?->original_url : File::find(2)?->media?->first()?->original_url;
    }
}
