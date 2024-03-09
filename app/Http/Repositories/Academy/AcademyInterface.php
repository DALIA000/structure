<?php

namespace App\Http\Repositories\Academy;

use App\Http\Repositories\Base\BaseInterface;

interface AcademyInterface extends BaseInterface
{
    public function models($request);
}
