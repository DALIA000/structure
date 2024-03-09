<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpamSection extends Model
{
    use HasFactory;
    use \Localizable;
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

    // locales columns
    public static $locales_columns = [
    ];

    public function spams()
    {
        return $this->hasMany(Spam::class);
    }
}
