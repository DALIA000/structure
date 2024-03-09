<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionLive extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
        'data' => 'json'
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

    public function session()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
