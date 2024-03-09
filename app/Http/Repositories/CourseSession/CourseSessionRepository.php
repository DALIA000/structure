<?php

namespace App\Http\Repositories\CourseSession;

use App\Http\Repositories\{
    Base\BaseRepository,
};
use App\Http\Resources\SessionsListResource;
use App\Models\{
    CourseSession,
    User,
};
use App\Services\JWTGenerator;

class CourseSessionRepository extends BaseRepository implements CourseSessionInterface
{
    public $loggedinUser;

    public function __construct(CourseSession $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('course_id') && $request->course_id !== null) {
                $query->where('course_id' ,$request->course_id);
            }
            
            if ($request->exists('status') && $request->status !== null) {
                $query->where('status' ,$request->status);
            }

            if ($request->exists('date_from') && $request->date_from !== null) {
                $query->whereDate('date', '<=', $request->date_from);
            }

            if ($request->exists('date_to') && $request->date_to !== null) {
                $query->whereRaw('date + INTERVAL 40 MINUTE>= ?', [$request->date_from]);
            }

            // where date + 40 > time_to
            if ($request->exists('time_from') && $request->time_from !== null) {                
                $query->whereTime('time', '<=', $request->time_from);
            }

            if ($request->exists('time_to') && $request->time_to !== null) {
                $query->whereRaw('time + INTERVAL 40 MINUTE >= ?', [$request->time_from]);
            }
            
            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('course.video.user', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }

            if ($request->exists('video_id') && $request->video_id !== null) {
                $query->whereHas('course', function ($query) use ($request) {
                    $query->where('video_id', $request->video_id);
                });
            }

            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('course', function ($query) use ($request) {
                        $query->where('status_id', $request->status_id);
                    })
                    ->orWhereHas('course.video', function ($query) use ($request) {
                        $query->where('user_id', $this->loggedinUser?->id);
                    });
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('course.video.user.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('course.video.user.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        // dd($models->toSql());

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
