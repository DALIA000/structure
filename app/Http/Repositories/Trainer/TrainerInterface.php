<?php

namespace App\Http\Repositories\Trainer;

use App\Http\Repositories\Base\BaseInterface;

interface TrainerInterface extends BaseInterface
{
    public function models($request);
}
