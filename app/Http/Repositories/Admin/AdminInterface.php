<?php

namespace App\Http\Repositories\Admin;
use App\Http\Repositories\Base\BaseInterface;

interface AdminInterface extends BaseInterface{
    public function models($request);
}
