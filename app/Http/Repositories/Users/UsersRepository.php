<?php

namespace App\Http\Repositories\Users;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Status;
use App\Models\User;
use DB;

class UsersRepository extends BaseRepository implements UsersInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

public function models($request)
{
    $model = $this->model->with('user_type')->where(function ($query) use ($request) {
        if ($request->exists('block')) {
            $query->whereIn('id', function ($subQuery) {
                $subQuery->select('user_id')
                    ->from('blocks');
            });
        }
        if ($request->exists('username') && $request->username !== null) {
            $query->where('username', $request->username);
        }
        if ($request->exists('email') && $request->email !== null) {
            $query->where('email', $request->email);
        }
         if ($request->exists('id') && $request->id !== null) {
            $query->where('id', $request->id);
        }
    });

    $model = $request->per_page ? $model->paginate($request->per_page) : $model->get();
    return $model;
}
}
