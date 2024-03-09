<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Casts\TimeCast;

class CourseSession extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'time' => TimeCast::class
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
