<?php

namespace App\Http\Repositories\AcademyPresident;

use App\Http\Repositories\AcademyPresident\AcademyPresidentInterface;
use App\Http\Repositories\{
    Academy\AcademyInterface,
    Base\BaseRepository,
};
use App\Models\{
    Academy,
    AcademyPresident,
};
use DB;

class AcademyPresidentRepository extends BaseRepository implements AcademyPresidentInterface
{
    public $loggedinUser;

    public function __construct(AcademyPresident $model, private Academy $academy, private AcademyInterface $AcademyI)
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

            if ($request->exists('academy_id') && $request->academy_id !== null) {
                $query->where('academy_id', $request->academy_id);
            }

            if ($request->exists('academy') && $request->academy !== null) {
                $query->whereHas('academy', function ($query) use ($request) {
                    $query->where('academy', $request->academy);
                });
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
        $academy = $this->AcademyI->findById($id);
        if (!$academy) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->academy)])]]];
        }

        $academy_president = $academy->academy_president;
        if ($academy_president) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.hasPresident', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $academy) {
            $model = $academy->academy_president()->create([
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
        $academy = $this->AcademyI->findById($id);
        if (!$academy) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->academy)])]]];
        }

        $model = $academy->academy_president;
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('full_name') && $request->full_name !== null) {
            $model->full_name = $request->full_name;
        }

        if ($request->exists('media')) {
            if ($request->media == null) {
                $model->media()->delete();
            }else {
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
