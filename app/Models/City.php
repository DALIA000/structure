<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class City extends Model
{
    use HasFactory;
    use Localizable;
    use SoftDeletes;
    use CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
        'country_id' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['locales'];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'name',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
