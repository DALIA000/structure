<?php

namespace App\Http\Repositories\Promote;

// use App\Events\UserPromoteddVideo;
use App\Http\Repositories\Promote\PromoteInterface;
use App\Http\Repositories\Status\StatusInterface;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\User\UserInterface;
use App\Models\Promote;

class PromoteRepository extends BaseRepository implements PromoteInterface
{
    public $loggedinUser;

    public function __construct(Promote $model, private StatusInterface $StatusI, public UserInterface $UserI)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('user_id') && $request->user_id !== null) {
                if ($request->exists('model_type') && $request->model_type !== null) {
                    switch ($request->model_type) {
                        case Video::class:
                        default:
                            $query->whereHas('video', function ($query) use ($request) {
                                $query->where('user_id', $request->user_id);
                            });
                            break;
                    }
                }
            }
            
            if ($request->exists('promotable_id') && $request->promotable_id !== null) {
                $query->where('promotable_id', $request->promotable_id);
            }

            if ($request->exists('promotable_type') && $request->promotable_type !== null) {
                $query->where('promotable_type', $request->promotable_type);
            }

            if ($request->exists('status') && $request->status !== null) {
                $query->where('status', $request->status);
            }

            if ($request->exists('user_type') && $request->user_type !== null) {
                $query->whereJsonContains('target->user_types', $request->user_type);
            }

            if ($request->exists('city') && $request->city !== null) {
                $query->whereJsonContains('target->cities', $request->city);
            }

            if ($request->exists('type') && $request->type !== null) {
                switch ($request->type) {
                    case '1':
                        $query->where('promotable_type', Video::class);
                        break;

                    default:
                        $query->where('promotable_type', Video::class);
                        break;
                }
            }

            if ($request->exists('model_type') && $request->model_type !== null) {
                $query->where([
                    'promotable_id' => $request->model_id,
                    'promotable_type' => $request->model_type,
                ]);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models->with($request->with ?? [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request) // promote
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = \DB::transaction(function () use ($request, $user) {
            $model = $this->model->updateOrCreate([
                'promotable_id' => $request->promotable_id,
                'promotable_type' => $request->promotable_type,
                'starts_at' => $request->starts_at,
                'ends_at' => $request->ends_at,
                'target' => $request->target,
            ]);

            $model->statistic()->create();

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->wasRecentlyCreated) {
            // UserPromoteddVideo::dispatch($model);
        }

        return ['status' => true, 'data' => $model];
    }
}
