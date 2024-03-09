<?php

namespace App\Http\Repositories\Invoice;

use App\Http\Repositories\{
    Base\BaseRepository,
    Invoice\InvoiceInterface
};
use App\Models\{
    Invoice,
};
use App\Models\Course;
use App\Services\LoggedinUser;

class InvoiceRepository extends BaseRepository implements InvoiceInterface
{
    public $loggedinUser;

    public function __construct(Invoice $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            /* if ($request->exists('user_id') && $request->user_id !== null) {
                $query->whereHas('invoicable', function ($query) use ($request) {
                    $request->where('user_id', $request->user_id);
                });
            } */

            if ($request->exists('invoicable_id') && $request->invoicable_id !== null) {
                $query->where('invoicable_id', $request->invoicable_id);
            }
            
            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }
        });

        $models = $models->with($request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $model = $this->model->updateOrCreate([
            'invoicable_id' => $request->invoicable_id,
            'model_id' => $request->model_id,
        ], [
            'invoicable_id' => $request->invoicable_id,
            'model_id' => $request->model_id,
            'cost' => $request->cost,
            'profit_margin' => $request->profit_margin,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function pay($id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        $invoicable = $model->invoicable;
        switch ($invoicable) {
            case $invoicable instanceof Course:
                $user_id = $invoicable->video->user_id;
                break;
                
            default:
                $user_id = $invoicable->user_id;
                break;
        }

        if ($user_id !== $this->loggedinUser?->id) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        \DB::transaction(function () use ($model) {
            $model->update([
                'status_id' => 1
            ]);
            
            $model->invoicable->update([
                'status_id' => 1
            ]);
        });

        return ['status' => true, 'data' => []];
    }
}
