<?php

namespace App\Http\Repositories\ChangeUserTypeRequest;

use App\Http\Repositories\ChangeUserTypeRequest\ChangeUserTypeRequestInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\ChangeUserTypeRequest;

class ChangeUserTypeRequestRepository extends BaseRepository implements ChangeUserTypeRequestInterface
{
    public $loggedinUser;

    public function __construct(ChangeUserTypeRequest $model, private StatusInterface $StatusI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('status') && $request->status !== null) {
                $query->where('status_id', $request->status);
            }
            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->where('user_id', $request->user_id);
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }
}
