<?php

namespace App\Http\Repositories\FinancialSetting;

use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    FinancialSetting,
};
use App\Services\LoggedinUser;

class FinancialSettingRepository extends BaseRepository implements FinancialSettingInterface
{
    public $loggedinUser;

    public function __construct(FinancialSetting $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('slug') && $request->slug !== null) {
                $query->where('slug', $request->slug);
            }
        });

        $models = $models->with($request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function update($request, $slug)
    {
        $model = $this->findBySlug($slug);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('value') && $request->value !== null) {
            $model->value = $request->value;
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
