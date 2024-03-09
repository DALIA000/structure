<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use App\Traits\Localizable;

class UserType extends Model
{
    use SoftDeletes;
    use CascadesDeletes;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = ['users'];

    // prevent if any exists
    public static $cascade = ['users'];

    // localizables
    public static $localizables = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user_type_preferences()
    {
        return $this->hasMany(UserTypePreference::class);
    }

    public function preferences()
    {
        return $this->hasManyThrough(Preference::class, UserTypePreference::class, 'user_type_class', 'id', 'user_type', 'preference_id');
    }
}
