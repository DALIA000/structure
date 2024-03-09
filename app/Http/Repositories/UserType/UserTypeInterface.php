<?php 

namespace App\Http\Repositories\UserType;

use App\Http\Repositories\Base\BaseInterface;

interface UserTypeInterface extends BaseInterface
{
  public function models($request);
}