<?php

namespace App\Traits;

use App\Models\{
    Model,
};

trait Modelable
{
    public static function model()
    {
        return Model::where('type', SELF::class)->first();
    }
}
