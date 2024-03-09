<?php
namespace App\Http\Repositories\Users;
use App\Http\Repositories\Base\BaseInterface;

interface UsersInterface extends BaseInterface{

    public function models($request);
}
