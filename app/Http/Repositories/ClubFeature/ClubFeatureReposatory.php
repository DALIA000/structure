<?php

namespace App\Http\Repositories\ClubFeature;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\ClubFeature\ClubFeatureInterface;
use App\Models\ClubFeature;
use App\Models\Admin;
use Illuminate\Http\Request;

class ClubFeatureReposatory extends BaseRepository implements ClubFeatureInterface
{
    public $loggedinUser;

    public function __construct(ClubFeature $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('club_id') && $request->club_id !== null) {
                $query->where('club_id', $request->club_id);
            }

            if ($request->exists('username') && $request->username !== null) {
                $query->whereHas('club.account', function ($query) use ($request) {
                    $query->where('username', $request->username);
                });
            }
        });

        // prevent blocked accounts
        $models->whereNot(function ($query) use ($request) {
            $query->whereHas('club.account.blocks', function ($query) use ($request) {
                $query->where('blockable_id', $this->loggedinUser?->id);
            })->orWhereHas('club.account.blocked', function ($query) use ($request) {
                $query->where('user_id', $this->loggedinUser?->id);
            });
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function update($request, $id)
    {
        if ($this->loggedinUser instanceof Admin) {
            $model = $this->model->where('club_id', $id)->updateOrCreate([
                'club_id' => $id
            ]);
        } else {
            $model = $this->model->where(['club_id' => $this->loggedinUser?->user?->id])->updateOrCreate([
                'club_id' => $this->loggedinUser?->user?->id
            ]);
        }

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }        

        $model = \DB::transaction(function () use ($request, $model) {
            if ($request->locales) {
                setLocales($this->model, $request->locales, $model->id);
            }
            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.update', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }
}
