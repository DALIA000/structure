<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Urlable;

class Image extends Model
{
  use SoftDeletes;
  use CascadesDeletes;
  use Urlable;

  public const AVAILABLE_CLASSES = [
    // 'admins' => \App\Models\Admin::class,
  ];

  protected $guarded = [];

  protected $casts = [
  ];

  // related files
  public static $files = ['image'];

  // cascade delete
  protected $cascadeDeletes = [];

  // prevent if any exists
  public static $cascade = [];

  public static function is_available($id, $ignore_type, $ignore_id)
  {
    $count = SELF::where(function ($query) use ($id, $ignore_type, $ignore_id) {
      $query->where('id', $id);

      if ($ignore_type && $ignore_id) {
        $query->where('imagable_id', false)
          ->orWhere(function ($query) use ($id, $ignore_type, $ignore_id) {
            $query->where(['id' => $id, 'imagable_id' => $ignore_id, 'imagable_type' => $ignore_type]);
          });
      } else {
        $query->where('imagable_id', false);
      }
    })->count();

    return $count;
  }

  public function imageable()
  {
    return $this->morphTo();
  }
}
