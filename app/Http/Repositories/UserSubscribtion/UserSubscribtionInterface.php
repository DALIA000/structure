<?php
namespace App\Http\Repositories\UserSubscribtion;

use App\Http\Repositories\Base\BaseInterface;

interface UserSubscribtionInterface extends BaseInterface{
    public function plan_subscribtions($request);
    public function course_subscribtions($request);
    public function competition_subscribtions($request);
}
