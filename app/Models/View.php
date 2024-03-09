<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'viewable_id' => 'integer',
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

    public function viewable() 
    {
        return $this->morphTo();
    } 
}
