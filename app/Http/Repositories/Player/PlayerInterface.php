<?php

namespace App\Http\Repositories\Player;

use App\Http\Repositories\Base\BaseInterface;

interface PlayerInterface extends BaseInterface
{
    public function models($request);
}
