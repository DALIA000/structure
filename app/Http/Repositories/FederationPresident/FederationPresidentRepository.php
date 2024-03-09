<?php

namespace App\Http\Repositories\FederationPresident;

use App\Http\Repositories\FederationPresident\FederationPresidentInterface;
use App\Http\Repositories\{
    Federation\FederationInterface,
    Base\BaseRepository,
};
use App\Models\{
    Federation,
    FederationPresident,
};
use DB;

class FederationPresidentRepository extends BaseRepository implements FederationPresidentInterface
{
    public $loggedinUser;

    public function __construct(FederationPresident $model, private Federation $federation, private FederationInterface $FederationI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('full_name', 'like', "%{$request->search}%");
                });
            }

            if ($request->exists('federation_id') && $request->federation_id !== null) {
                $query->where('federation_id', $request->federation_id);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request, $id)
    {
        $federation = $this->FederationI->findById($id);
        if (!$federation) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->federation)])]]];
        }

        $federation_president = $federation->federation_president;
        if ($federation_president) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.hasPresident', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $federation) {
            $model = $federation->federation_president()->create([
                'full_name' => $request->full_name,
            ]);

            \Media::where('id', $request->media)->update([
                'model_id' => $model->id,
                'model_type' => get_class($this->model),
            ]);

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function edit($request, $id)
    {
        $federation = $this->FederationI->findById($id);
        if (!$federation) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->federation)])]]];
        }

        $model = $federation->federation_president;
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('full_name') && $request->full_name !== null) {
            $model->full_name = $request->full_name;
        }

        if ($request->exists('media')) {
            if ($request->media == null) {
                $model->media()->delete();
            } else {
                $model->clearMediaCollectionExcept('media', \Media::where('id', $request->media)->first());
                \Media::where('id', $request->media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}