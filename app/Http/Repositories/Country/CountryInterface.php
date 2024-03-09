<?php

namespace App\Http\Repositories\Country;

use App\Http\Repositories\Base\BaseInterface;

interface CountryInterface extends BaseInterface
{
    public function models($request);
}
