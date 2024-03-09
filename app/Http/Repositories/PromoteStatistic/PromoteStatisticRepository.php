<?php

namespace App\Http\Repositories\PromoteStatistic;

use App\Http\Repositories\PromoteStatistic\PromoteStatisticInterface;
use App\Http\Repositories\Status\StatusInterface;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\User\UserInterface;
use App\Models\PromoteStatistic;

class PromoteStatisticRepository extends BaseRepository implements PromoteStatisticInterface
{
    public $loggedinUser;

    public function __construct(PromoteStatistic $model, private StatusInterface $StatusI, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('promote_id') && $request->promote_id !== null) {
                $query->where('promote_id', $request->promote_id);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models->with($request->with ?? [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
