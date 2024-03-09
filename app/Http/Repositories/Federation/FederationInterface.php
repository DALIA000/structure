<?php

namespace App\Http\Repositories\Federation;

use App\Http\Repositories\Base\BaseInterface;

interface FederationInterface extends BaseInterface
{
    public function models($request);
}
