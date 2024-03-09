<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Accountable;

class Fan extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
