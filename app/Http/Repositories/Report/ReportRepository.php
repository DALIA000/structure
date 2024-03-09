<?php

namespace App\Http\Repositories\Report;

use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Report,
    Video,
    Course,
    Comment,
    User,
};
use Musonza\Chat\Models\Message;

class ReportRepository extends BaseRepository implements ReportInterface
{
    public $loggedinUser;

    public function __construct(Report $model, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->exists('reportable_id') && $request->reportable_id !== null) {
                $query->where('reportable_id', $request->reportable_id);
            }

            if($request->exists('status') && $request->status == 0){
                $query-> where ('read_at', null);
            }

            if($request->exists('type') && $request->type !== null){
                switch($request->type){
                    case 'videos':
                        $query->where('reportable_type', Video::class);
                        break;
                    case 'courses':
                        $query->where('reportable_type', Course::class);
                        break;
                    case 'comments':
                        $query->where('reportable_type', Comment::class);
                        break;
                    case 'accounts':
                        $query->where('reportable_type', User::class);
                        break;
                    case 'messages':
                        $query->where('reportable_type', Message::class);
                        break;
                }
            }

            if ($request->exists('status') && $request->status == 1) {
               $query->whereNotNull('read_at');
            }

            if ($request->exists('reportable_username') && $request->reportable_username !== null) {
                $query->whereHas('reportable', function ($query) use ($request) {
                    $query->where('username', 'like', "%{$request->reportable_username}%");
                });
            }

            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function read($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->update([
            'read_at' => now()
        ]);

        return ['status' => true, 'data' => []];
    }

    public function unread($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->update([
            'read_at' => null,
        ]);

        return ['status' => true, 'data' => []];
    }

    public function create($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->reports()->updateOrCreate([
            'user_id' => $id,
            'reportable_id' => $request->reportable_id,
            'reportable_type' => $request->reportable_type,
            'spam_id' => $request->spam_option_id,
            'note' => $request->note,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        // DeleteReportable::dispatch($model);
        return ['status' => true, 'data' => $model];
    }
}
