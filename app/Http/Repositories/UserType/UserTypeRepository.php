<?php

namespace App\Http\Repositories\UserType;

use App\Http\Repositories\Base\BaseRepository;
use App\Models\UserType;

class UserTypeRepository extends BaseRepository implements UserTypeInterface
{
    public function __construct(UserType $model)
    {
        $this->model = $model;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('id') && $request->id !== null) {
                $query->where('id', $request->id);
            }

            if ($request->exists('user_type') && $request->user_type !== null) {
                $query->where('user_type', $request->user_type);
            }

            if ($request->exists('available') && $request->available !== null) {
                $query->where('available', $request->available);
            }
        });

        $models = $this->getWith($models, $request->with ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }
}
