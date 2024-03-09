<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as Model;
use App\Traits\Localizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Role extends Model
{
    use HasFactory;
    use Localizable;
    use CascadesDeletes;

    protected $guarded = [];

    // cascade delete
    protected $cascadeDeletes = ['locales'];

    // prevent if any exists
    public static $cascade = ['users'];

    public function getLocalesGroupsAttribute()
    {
        return $this->locales()->get([
          'id',
          'locale',
          'name',
        ]);//->groupBy('locale')->map(fn($i) => $i ? $i[0] : null);
    }

    public static $locales_columns = [
        'id',
        'locale',
        'name',
    ];

    /* public function admins()
    {
      return $this->hasMany(Admin::class);
    } */
}
