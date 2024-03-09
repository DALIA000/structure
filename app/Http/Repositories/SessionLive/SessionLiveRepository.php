<?php

namespace App\Http\Repositories\SessionLive;

use App\Http\Repositories\{
    Base\BaseRepository,
};
use App\Http\Repositories\CourseSession\CourseSessionInterface;
use App\Http\Resources\FileResource;
use App\Http\Resources\SessionsListResource;
use App\Http\Resources\UserFileResource;
use App\Models\{
    File,
    SessionLive,
    User,
};

use App\Services\JWTGenerator;

class SessionLiveRepository extends BaseRepository implements SessionLiveInterface
{
    public $loggedinUser;

    public function __construct(SessionLive $model, public CourseSessionInterface $CourseSessionI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('course_id') && $request->course_id !== null) {
                $query->whereHas('session', function ($query) use ($request) {
                    $query->where('course_id', $request->course_id);
                });
            }
            
            if ($request->exists('status') && $request->status !== null) {
                $query->where('status' ,$request->status);
            }

            if ($request->exists('date_from') && $request->date_from !== null) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->exists('date_to') && $request->date_to !== null) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('session.course.video.user', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }

            if ($request->exists('video_id') && $request->video_id !== null) {
                $query->whereHas('session.course', function ($query) use ($request) {
                    $query->where('video_id', $request->video_id);
                });
            }

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('session.course', function ($query) use ($request) {
                        $query->where('status_id', $request->status_id);
                    })
                    ->orWhereHas('session.course.video', function ($query) use ($request) {
                        $query->where('user_id', $this->loggedinUser?->id);
                    });
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('session.course.video.user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('session.course.video.user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $models = $this->CourseSessionI->models($request);

        if (!$models) {
            return ['status' => false, 'errors' => ['error' => trans('crud.notfound')]];
        }
        if (!$models['status']) {
            return ['status' => false, ['errors' => $models['errors']]];
        }

        $model = $models['data']?->first();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound')]]];
        }

        $model = $this->model->create([
            'session_id' => $model->id,
            'status' => $request->status_id,
            // 'data' => $request->data,
        ]);

        // create room with moderator JWT

        $jwt = JWTGenerator::generateJWT($model, $this->loggedinUser, true);

        return ['status' => true, 'data' => ['model' => $model, 'jwt' => $jwt]];
    }

    public function join($session_id)
    {
        $model = $this->findWhere(['status' => 1, 'session_id' => $session_id]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound')]]];
        }

        // join room with moderator JWT
        $jwt = JWTGenerator::generateJWT($model, $this->loggedinUser, false);

        return ['status' => true, 'data' => ['model' => $model, 'jwt' => $jwt]];
    }
}
