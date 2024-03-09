<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spam extends Model
{
    use HasFactory;
    use \Localizable;
    use \SoftDeletes;
    use \CascadesDeletes;

    protected $guarded = [];

    protected $table = 'spams';

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
        'locale',
        'name',
    ];

    public function spam_section()
    {
        return $this->belongsTo(SpamSection::class);
    }
}
