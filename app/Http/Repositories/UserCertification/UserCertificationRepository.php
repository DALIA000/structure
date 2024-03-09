<?php

namespace App\Http\Repositories\UserCertification;

use App\Http\Repositories\UserCertification\UserCertificationInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\UserCertification;
use DB;
use Str;

class UserCertificationRepository extends BaseRepository implements UserCertificationInterface
{
    public $loggedinUser;

    public function __construct(UserCertification $model)
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

    public function create($request, $user_id)
    {
        $model = DB::transaction(function () use ($request) {            
            $model = $this->model->create([
              'user_id' => $request->user_id,
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
        
        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
