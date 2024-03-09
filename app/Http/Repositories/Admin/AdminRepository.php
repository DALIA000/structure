<?php

namespace App\Http\Repositories\Admin;

use App\Http\Repositories\Admin\AdminInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Admin;
use App\Services\ResponseService;
use DB;
use Hash;
use Illuminate\Support\Carbon;

class AdminRepository extends BaseRepository implements AdminInterface
{
    public $user_type_class;
    public $loggedinUser;

    public function __construct(Admin $model, public ResponseService $ResponseService)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

     public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%");
                });}

            if ($request->exists('username') && $request->username !== null) {
                    $query->where('username', $request->username);
            }

              if ($request->exists('email') && $request->email !== null) {
                    $query->where('email', $request->email);
            }});

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
            $model = $this->model::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            if (!$model) {
                return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
            }

            $model->roles()->attach($request->role);

            return ['status' => true, 'data' => $model];
    }

    public function edit($request, $id)
    {
        $model = $this->model->find($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->exists('name') && $request->name !== null) {
            $model->name = $request->name;
        }
        if ($request->exists('username') && $request->username !== null) {
            $model->username = $request->username;
        }
        if ($request->exists('email') && $request->email !== null) {
            $model->email = $request->email;
        }
        if ($request->exists('password') && $request->password !== null) {
            $model->password = Hash::make($request->password);
        }
        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
