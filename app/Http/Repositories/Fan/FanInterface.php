<?php

namespace App\Http\Repositories\Fan;

use App\Http\Repositories\Base\BaseInterface;

interface FanInterface extends BaseInterface
{
    public function models($request);
}
