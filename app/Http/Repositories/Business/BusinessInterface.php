<?php

namespace App\Http\Repositories\Business;

use App\Http\Repositories\Base\BaseInterface;

interface BusinessInterface extends BaseInterface
{
    public function models($request);
}
