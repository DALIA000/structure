<?php

namespace App\Http\Repositories\ClubPlan;

use App\Http\Repositories\{
    Base\BaseRepository,
    ClubPlan\ClubPlanInterface,
    Club\ClubInterface,
};
use App\Models\{
    ClubPlan,
};
use DB;

class ClubPlanRepository extends BaseRepository implements ClubPlanInterface
{
    public $loggedinUser;

    public function __construct(ClubPlan $model, public ClubInterface $ClubI)
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

            if ($request->exists('club') && $request->club !== null) {
                $query->whereHas('club.account', function ($query) use ($request) {
                    $query->where('username', $request->club);
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

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $request->merge(['withCount' => ['plans']]);
        $club = $this->ClubI->findByWith('id', $request->club_id, $request);

        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($club->plans_count >= 3) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.plansLimit', ['model' => trans_class_basename($this->model)])]]];
        }

        $models = DB::transaction(function () use ($request) {
            $models = [];
            foreach ($request->plans as $key => $plan) {
                $model = $this->model->create([
                    'club_id' => $request->club_id,
                    'club_plan_type_id' => $plan['id'],
                    'price' => $plan['price'],
                ]);

                if (isset($plan['locales'])) {
                    $this->setLocales($model, $plan['locales']);
                }
                array_push($models, $model);
            }

            return $models;
        });

        if (!$models) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $models];
    }

    public function update($request)
    {
        $request->merge(['with' => [
            'plans'
        ]]);

        $club = $this->ClubI->findByWith('id', $request->club_id, $request);

        if (!$club) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        
        $club_plans = $club->plans;

        foreach (array_keys($request->plans) as $key) {
            $plan = $club_plans->where('club_plan_type_id', $key)->first();
            if(!$plan){
                $plan = $club->plans()->create([
                    'club_plan_type_id' => $key,
                    'price' => $request->plans[$key]['price']
                ]);
            }else{
                $plan?->update([
                    'price' => $request->plans[$key]['price']
                ]);
            }

            if (isset($plan['locales'])) {
                $this->setLocales($plan, $request->plans[$key]['locales']);
            }
        }

        return ['status' => true, 'data' => $club->plans];
    }
}
