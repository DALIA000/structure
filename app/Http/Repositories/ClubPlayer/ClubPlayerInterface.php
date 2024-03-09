<?php

namespace App\Http\Repositories\ClubPlayer;

use App\Http\Repositories\Base\BaseInterface;

interface ClubPlayerInterface extends BaseInterface
{
    public function models($request);
}
