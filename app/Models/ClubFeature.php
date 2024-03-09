<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubFeature extends Model
{
    use HasFactory;
    use Localizable;

    protected $guarded= [];

    // locales columns
    public static $locales_columns = [
        'id',
        'locale',
        'description'
    ];

    public function club(){
        return $this->belongsTo(Club::class);
    }
}
