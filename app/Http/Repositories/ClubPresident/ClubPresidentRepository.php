<?php

namespace App\Http\Repositories\ClubPresident;

use App\Http\Repositories\ClubPresident\ClubPresidentInterface;
use App\Http\Repositories\{
    Club\ClubInterface,
    Base\BaseRepository,
};
use App\Models\{
    Club,
    ClubPresident,
};
use DB;

class ClubPresidentRepository extends BaseRepository implements ClubPresidentInterface
{
    public $loggedinUser;

    public function __construct(ClubPresident $model, private Club $club, private ClubInterface $ClubI)
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

            if ($request->exists('club_id') && $request->club_id !== null) {
                $query->where('club_id', $request->club_id);
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
        $club = $this->ClubI->findById($id);
        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->club)])]]];
        }

        $club_president = $club->club_president;
        if ($club_president) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.hasPresident', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $club) {
            $model = $club->club_president()->create([
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
        $club = $this->ClubI->findById($id);
        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->club)])]]];
        }

        $model = $club->club_president;
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
