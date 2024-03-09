<?php

namespace App\Http\Repositories\Block;

use App\Events\UserBlocked;
use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Block,
};
use App\Services\LoggedinUser;

class BlockRepository extends BaseRepository implements BlockInterface
{
    public $loggedinUser;

    public function __construct(Block $model, public UserInterface $UserI)
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
            
            if ($request->exists('blockable_id') && $request->blockable_id !== null) {
                $query->where('blockable_id', $request->blockable_id);
            }

            if ($request->exists('blockable_username') && $request->blockable_username !== null) {
                $query->whereHas('blockable', function ($query) use ($request) {
                    $query->where('username', 'like', "%{$request->blockable_username}%");
                }
                );
            }
            
            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }

            if ($request->exists('search') && $request->search !== null) {
                $query->whereHas('blockable', function ($query) use ($request) {
                    $query->where('username', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('keywords', 'like', "%{$request->search}%");
                });
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->blocks()->updateOrCreate([
            'blockable_id' => $request->blockable_id,
            'blockable_type' => $request->blockable_type,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        UserBlocked::dispatch($model);

        return ['status' => true, 'data' => $model];
    }

    public function unblock($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->blocks()->where([
            'blockable_id' => $request->blockable_id,
            'blockable_type' => $request->blockable_type,
        ])->delete();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => []];
    }
}
