<?php

namespace App\Http\Repositories\ClubPlayer;

use App\Http\Repositories\ClubPlayer\ClubPlayerInterface;
use App\Http\Repositories\{
    Club\ClubInterface,
    Base\BaseRepository,
};
use App\Models\ClubPlayer;
use DB;

class ClubPlayerRepository extends BaseRepository implements ClubPlayerInterface
{
    public $loggedinUser;

    public function __construct(ClubPlayer $model, private ClubInterface $ClubI)
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

            if ($request->exists('club') && $request->club !== null) {
                $query->whereHas('club.account', function ($query) use ($request)
                {
                    $query->where('username', $request->club);
                });
            }

            if ($request->exists('player_position_id') && $request->player_position_id !== null) {
                $query->where('player_position_id', $request->player_position_id);
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


    public function create($request)
    {
        $club = $this->ClubI->findById($request->club);

        $model = DB::transaction(function () use ($request, $club) {
            $model = $club->club_players()->create([
                'full_name' => $request->full_name,
                'player_position_id' => $request->player_position,
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
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->club_id !== $this->loggedinUser->user->id) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('full_name') && $request->full_name !== null) {
            $model->full_name = $request->full_name;
        }

        if ($request->exists('player_position') && $request->player_position !== null) {
            $model->player_position_id = $request->player_position;
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

    public function club_player_delete($request, $id)
    {
        $models = $this->loggedinUser?->user?->club_players()->where('id', $id);

        if (!$models->count()) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $models->first();
        if ($this->delete($model?->id)) {
            return ['status' => true, 'data' => []];
        }

        return ['status' => false, 'errors' => ['error' => [trans('crud.delete', ['model' => trans_class_basename($this->model)])]]];
    }
}
