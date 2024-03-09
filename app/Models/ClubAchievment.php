<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\MediaTrait;

class ClubAchievment extends Model implements HasMedia
{
    use HasFactory;
    use MediaTrait;

    protected $cascadeDeletes = ['images'];

    protected $guarded = [];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
