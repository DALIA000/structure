<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Country extends Model
{
    use HasFactory;
    use Localizable;
    use CascadesDeletes;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'status' => 'integer',
        'vat' => 'float',
        'currency_id' => 'integer',
    ];

    // related files
    public static $files = ['image'];

    // cascade delete
    protected $cascadeDeletes = ['locales', 'cities'];

    // prevent if any exists
    public static $cascade = ['cities'];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'name',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
