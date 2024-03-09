<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoTag extends Model
{
    protected $guarded = [];

    protected $casts = [
        'video_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
