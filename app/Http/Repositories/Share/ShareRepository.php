<?php

namespace App\Http\Repositories\Share;

use App\Http\Repositories\Share\ShareInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Model;
use App\Models\Share;

class ShareRepository extends BaseRepository implements ShareInterface
{
    public $loggedinUser;

    public function __construct(Share $model, public Model $modelM)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function create($request)
    {
        $model = $this->model->create([
            'ip' => $request->ip,
            'sharable_id' => $request->model_id,
            'sharable_type' => $request->sharable_type,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => trans('crud.create', ['model' => $this->modelM])];
        };

        return ['status' => true, 'data' => $model];
    }
}