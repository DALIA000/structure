<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
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

    public static function is_available($id, $ignore_type, $ignore_id)
    {
      $count = SELF::where(function ($query) use ($id, $ignore_type, $ignore_id)
      {
        $query->where('id', $id);

        if ($ignore_type && $ignore_id) {
          $query->where('documentable_id', false)
                ->orWhere(function ($query) use ($id, $ignore_type, $ignore_id)
                {
                  $query->where(['id' => $id, 'documentable_id' => $ignore_id, 'documentable_type' => $ignore_type]);
                });
        }else{
          $query->where('documentable_id', null);
        }
      })->count();

      return $count;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

