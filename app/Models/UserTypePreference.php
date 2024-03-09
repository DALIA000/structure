<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserTypePreference extends Model
{
    use HasFactory;
    use \SoftDeletes;
    use \CascadesDeletes;

    protected $guarded = [];

    protected $casts = [
    ];

    // related files
    public static $files = [];

    // cascade delete
    protected $cascadeDeletes = [];

    // prevent if any exists
    public static $cascade = [];

    // localizables
    public static $localizables = [];

    public function user_type()
    {
        return $this->belongsTo(UserType::class);
    }

    public function preference()
    {
        return $this->belongsTo(Preference::class);
    }
}
