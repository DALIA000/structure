<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use HasFactory;
    use Localizable;
    use SoftDeletes;
    use CascadesDeletes;

    protected $table = 'status';

    protected $guarded = [];

    protected $casts = [
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

    public function model_status()
    {
        return $this->hasMany(ModelStatus::class);
    }
}
