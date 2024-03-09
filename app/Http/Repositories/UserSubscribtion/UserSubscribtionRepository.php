<?php

namespace App\Http\Repositories\UserSubscribtion;

use App\Http\Repositories\Base\BaseRepository;
use App\Models\CompetitionSubscribtion;
use App\Models\CourseSubscribtion;
use App\Models\Subscribtion;

class UserSubscribtionRepository extends BaseRepository implements UserSubscribtionInterface
{
    public $loggedinUser;
    public $coursemodel = CourseSubscribtion::class;

    public function __construct(
        public Subscribtion $subscribtionModel,
        public CourseSubscribtion $courseSubscribtionModel,
        public CompetitionSubscribtion $competitionSubscribtionModel
    ) {
        $this->loggedinUser = app('loggedinUser');
    }

    public function subscribtions($model, $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => trans('auth.unauthenticated')]];
        }
        $model = $model::where('user_id', $user->id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => trans('notfound')]];
        }
        [$sort, $order] = $this->setSortParams($request);
        $model->orderBy($sort, $order);

        $model = $request->per_page ? $model->paginate($request->per_page) : $model->get();

        return ['status' => true, 'data' => $model];
    }

    public function plan_subscribtions($request)
    {
        $model = $this->subscribtions($this->subscribtionModel, $request);
        return $model;
    }

    public function course_subscribtions($request)
    {
        $model = $this->subscribtions($this->courseSubscribtionModel, $request);
        return $model;
    }

    public function competition_subscribtions($request)
    {
        $model = $this->subscribtions($this->competitionSubscribtionModel, $request);
        return $model;
    }
}
