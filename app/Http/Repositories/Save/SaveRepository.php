<?php

namespace App\Http\Repositories\Save;

use App\Http\Repositories\{
    Base\BaseRepository,
    User\UserInterface,
};
use App\Models\{
    Save,
    Video,
    Blog,
    User
};
use App\Services\LoggedinUser;

class SaveRepository extends BaseRepository implements SaveInterface
{
    public $loggedinUser;

    public function __construct(Save $model, public UserInterface $UserI)
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
            
            if ($request->exists('savable_id') && $request->savable_id !== null) {
                $query->where('savable_id', $request->savable_id);
            }
            
            if ($request->exists('savable_username') && $request->savable_username !== null) {
                $query->whereHas('savable', function ($query) use ($request) {
                    $query->where('username', 'like',  "%{$request->savable_username}%");
                });
            }
            
            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }
            
            if ($request->exists('type') && $request->type !== null) {
                switch ($request->type) {
                    case '1':
                        $query->where('savable_type', Video::class);
                        break;
                    case '2':
                        $query->where('savable_type', Blog::class);
                        break;
                    case '3':
                        $query->where('savable_type', User::class);
                        if ($request->exists('search') && $request->search !== null) {
                            $query->whereHas('savable', function ($query) use ($request) {
                                $query->where('username', 'like', "%{$request->search}%")
                                    ->orWhere('email', 'like', "%{$request->search}%")
                                    ->orWhere('keywords', 'like', "%{$request->search}%");
                            });
                        }
                        break;
                    
                    default:
                        $query->where('savable_type', Video::class);
                        break;
                }
            } else {
                $query->where('savable_type', '!=', User::class);
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

        $model = $user->saves()->updateOrCreate([
            'savable_id' => $request->savable_id,
            'savable_type' => $request->savable_type,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function unsave($request, $id)
    {
        $user = $this->UserI->findById($id);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model = $user->saves()->where([
            'savable_id' => $request->savable_id,
            'savable_type' => $request->savable_type,
        ]);

        if ($model) {
            $model->delete();
        }

        return ['status' => true, 'data' => []];
    }
}
