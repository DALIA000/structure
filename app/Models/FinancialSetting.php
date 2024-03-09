<?php

namespace App\Models;

use App\Traits\Localizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class FinancialSetting extends EloquentModel
{
    use HasFactory;
    use Localizable;

    protected $guarded = [];

    protected $casts = [
    ];

    // locales columns
    public static $locales_columns = [
        'id',
        'name'
    ];
}
