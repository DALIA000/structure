<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscribtion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean'
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

    public function plan()
    {
        return $this->belongsTo(ClubPlan::class, 'plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club_member()
    {
        return $this->hasOne(ClubMember::class, 'user_id', 'user_id');
    }
}
