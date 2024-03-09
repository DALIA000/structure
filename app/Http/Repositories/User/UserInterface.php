<?php

namespace App\Http\Repositories\User;

use App\Http\Repositories\Base\BaseInterface;

interface UserInterface extends BaseInterface
{
    public function create($request);
}
