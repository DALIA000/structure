<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
        'type' => 'integer',
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = [];

    // prevent if any exists
    public static $cascade = [];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'name',
    ];

    public function user_type_preferences()
    {
        return $this->hasMany(UserTypePreference::class);
    }

    public function preference_type()
    {
        return $this->belongsTo(PreferenceType::class);
    }
}
