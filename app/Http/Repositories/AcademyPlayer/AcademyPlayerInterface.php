<?php

namespace App\Http\Repositories\AcademyPlayer;

use App\Http\Repositories\Base\BaseInterface;

interface AcademyPlayerInterface extends BaseInterface
{
    public function models($request);
}
