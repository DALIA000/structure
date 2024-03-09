<?php

namespace App\Http\Repositories\Permission;

use App\Http\Repositories\Permission\PermissionInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Permission;

class PermissionRepository extends BaseRepository implements PermissionInterface
{
    public $loggedinUser;

    public function __construct(Permission $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
        });

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
