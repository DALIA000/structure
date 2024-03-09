<?php

namespace App\Http\Repositories\Follow;

use App\Events\UserFollowed;
use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Follow,
};
use App\Services\LoggedinUser;

class FollowRepository extends BaseRepository implements FollowInterface
{
    public $loggedinUser;

    public function __construct(Follow $model, public UserInterface $UserI)
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
            
            if ($request->exists('is_pending') && $request->is_pending !== null) {
                $query->where('is_pending', $request->is_pending);
            }
            
            if ($request->exists('followable_id') && $request->followable_id !== null) {
                $query->where('followable_id', $request->followable_id);
            }

            if ($request->exists('followable_username') && $request->followable_username !== null) {
                $query->whereHas('followable', function ($query) use ($request) {
                    $query->where('username', $request->followable_username);
                }
                );
            }
            
            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request, $id, $is_pending = 1)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->follows()->updateOrCreate([
            'followable_id' => $request->followable_id,
            'followable_type' => $request->followable_type,
            'is_pending' => $is_pending,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        UserFollowed::dispatch($model);

        return ['status' => true, 'data' => $model];
    }

    public function unfollow($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->follows()->where([
            'followable_id' => $request->followable_id,
            'followable_type' => $request->followable_type,
        ])->delete();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => []];
    }

    public function accept($request, $username)
    {
        $loggedinUser = $this->loggedinUser;
        $user = $this->UserI->findByUsername($username);

        if (!$user || !$loggedinUser) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $loggedinUser->followers()->where(function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->update([
            'is_pending' => 0,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function reject($request, $username)
    {
        $loggedinUser = $this->loggedinUser;
        $user = $this->UserI->findByUsername($username);

        if (!$user || !$loggedinUser) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $loggedinUser->followers()->where(function ($query) use ($user) {
            $query->where('user_id', $user->id);
            $query->where('is_pending', 1);
        })->delete();

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }
}
