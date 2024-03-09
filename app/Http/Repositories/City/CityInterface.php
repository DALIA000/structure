<?php

namespace App\Http\Repositories\City;

use App\Http\Repositories\Base\BaseInterface;

interface CityInterface extends BaseInterface
{
    public function models($request);
}
