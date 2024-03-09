<?php

namespace App\Http\Repositories\Subscribe;

use App\Http\Repositories\ClubPlan\ClubPlanInterface;
use App\Http\Repositories\Subscribe\SubscribeInterface;
use App\Http\Repositories\{
    Base\BaseRepository,
};
use App\Http\Repositories\User\UserInterface;
use App\Models\{
    Subscribtion,
};
use DB;

class SubscribeRepository extends BaseRepository implements SubscribeInterface
{
    public $loggedinUser;

    public function __construct(Subscribtion $model, public ClubPlanInterface $ClubPlanI, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('user_id') && $request->user_id !== null) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->exists('user') && $request->user !== null) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('username', $request->user);
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

    public function create($request)
    {
        $plan = $this->ClubPlanI->findByWhere('id', $request->plan_id, [], function ($query) use ($request) {
                $query->whereHas('club.account.blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('club.account.blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$plan) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $user = $this->UserI->findById($request->user_id);
        $club = $this->ClubPlanI->findById($request->plan_id)?->club;

        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]]];
        }

        if ($user->has_active_plan) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.alreadySubscribed', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = DB::transaction(function () use ($request, $club) {
            $model = $this->model->create([
                'plan_id' => $request->plan_id,
                'user_id' => $request->user_id,
                'status' => 1
            ]);

            $club_member = $club->club_members()->where([
                'user_id' => $request->user_id,
            ])->first();

            if ($club_member) {
                $club_member->update([
                    'status' => 1
                ]);
            } else {
                $model->club_member()->create([
                    'user_id' => $request->user_id,
                    'club_id' => $club?->id,
                    'number' => 1 + ($club->latest_club_members?->number ?: 0),
                ]);
            }

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }
}
