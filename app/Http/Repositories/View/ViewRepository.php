<?php

namespace App\Http\Repositories\View;

use App\Http\Repositories\View\ViewInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Model;
use App\Models\View;

class ViewRepository extends BaseRepository implements ViewInterface
{
    public $loggedinUser;

    public function __construct(View $model, public Model $modelM)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function create($request)
    {
        $model = $this->model->create([
            'ip' => $request->ip,
            'viewable_id' => $request->viewable_id,
            'viewable_type' => $request->viewable_type,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => trans('crud.create', ['model' => $this->modelM])];
        };

        return ['status' => true, 'data' => $model];
    }
}