<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class VerificationCode extends Model
{
  use SoftDeletes;
  use CascadesDeletes;

  protected $guarded = [];

  protected $casts = [
  ];

  // related files
  public static $files = [];

  // cascade delete
  protected $cascadeDeletes = [];

  // prevent if any exists
  public static $cascade = [];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
