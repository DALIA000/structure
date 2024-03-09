<?php

namespace App\Http\Repositories\ChangeUserTypeRequest;

use App\Http\Repositories\Base\BaseInterface;

interface ChangeUserTypeRequestInterface extends BaseInterface
{
    public function models($request);
}
