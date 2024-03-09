<?php

namespace App\Http\Repositories\ClubAchievment;

use App\Http\Repositories\ClubAchievment\ClubAchievmentInterface;
use App\Http\Repositories\{
    Club\ClubInterface,
    Base\BaseRepository,
};
use App\Models\{
    Club,
    ClubAchievment,
};
use DB;

class ClubAchievmentRepository extends BaseRepository implements ClubAchievmentInterface
{
    public $loggedinUser;

    public function __construct(ClubAchievment $model, private Club $club, private ClubInterface $ClubI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%");
                });
            }

            if ($request->exists('club_id') && $request->club_id !== null) {
                $query->where('club_id', $request->club_id);
            }

            if ($request->exists('club') && $request->club !== null) {
                $query->whereHas('club.account', function ($query) use ($request) {
                    $query->where('username', $request->club);
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
        $club = $this->ClubI->findById($id);
        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->club)])]]];
        }

        $model = DB::transaction(function () use ($request, $club) {
            $model = $club->club_achievments()->create([
                'title' => $request->title,
                'year' => $request->year,
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
        $club = $this->ClubI->findById($request->club_id);
        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->ClubI->model)])]]];
        }

        $model = $club->club_achievments()->where('id', $id)->first();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->club)])]]];
        }

        if ($request->exists('title') && $request->title !== null) {
            $model->title = $request->title;
        }

        if ($request->exists('year') && $request->year !== null) {
            $model->year = $request->year;
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

    public function achievment_delete($request, $id)
    {
        $models = $this->loggedinUser?->user?->club_achievments()->where('id', $id);

        if (!$models->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        
        if ($this->delete($id)) {
            return ['status' => true, 'data' => []];
        }
        
        return ['status' => false, 'errors' => ['error' => [trans('crud.delete', ['model' => trans_class_basename($this->model)])]]];
    }
}
