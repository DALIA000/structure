<?php

namespace App\Http\Repositories\Status;

use App\Http\Repositories\Base\BaseInterface;

interface StatusInterface extends BaseInterface
{
    public function models($request);
}
