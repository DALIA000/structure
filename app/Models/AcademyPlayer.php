<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SoftDeletes;
use CascadesDeletes;
use App\Traits\Statusable;

class AcademyPlayer extends Model
{
    use HasFactory;
    use SoftDeletes;
    use CascadesDeletes;
    use Statusable;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
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

    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

}
