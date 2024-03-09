<?php

namespace App\Http\Repositories\Club;

use App\Http\Repositories\Base\BaseInterface;

interface ClubInterface extends BaseInterface
{
    public function models($request);
}
