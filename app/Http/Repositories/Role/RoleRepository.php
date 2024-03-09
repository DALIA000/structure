<?php

namespace App\Http\Repositories\Role;

use App\Http\Repositories\Role\RoleInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Role;
use DB;

class RoleRepository extends BaseRepository implements RoleInterface
{
    public $loggedinUser;

    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $roles = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('slug', 'like', '%'.$request->search.'%')
                          ->orWhereHas('locales', function ($query) use ($request) {
                              $query->where('name', 'like', '%'.$request->search.'%');
                          });
                });
            }
        });

        $roles = $this->getWith($roles, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $roles->orderBy($sort, $order);

        $roles = $request->per_page ? $roles->paginate($request->per_page) : $roles->get();

        return ['status' => true, 'data' => $roles];
    }

    public function create($request)
    {
        $model = DB::transaction(function () use ($request) {
            $first_key = array_key_first($request->locales);
            $model = $this->model->create([
                'name' => $request->locales[$first_key]['name'],
                'guard_name' => 'admin',
            ]);

            if ($request->exists('permissions')) {
                $model->syncPermissions($request->permissions);
            }

            if ($request->exists('locales')) {
                $this->setLocales($model, $request->locales);
            }

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return $model;
    }

    public function edit($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($model->id == 1) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.protected', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('locales')) {
            $this->setLocales($model, $request->locales, slug:true);
            $model->updated_at = now();
        }

        if ($request->exists('permissions')) {
            $model->syncPermissions($request->permissions);
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
