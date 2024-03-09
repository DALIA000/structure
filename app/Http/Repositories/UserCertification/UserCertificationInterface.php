<?php

namespace App\Http\Repositories\UserCertification;

use App\Http\Repositories\Base\BaseInterface;

interface UserCertificationInterface extends BaseInterface
{
    public function models($request);
}
