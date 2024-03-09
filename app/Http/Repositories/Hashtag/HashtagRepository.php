<?php

namespace App\Http\Repositories\Hashtag;

use App\Http\Repositories\{
    Base\BaseRepository,
    Hashtag\HashtagInterface,
};
use App\Models\{
    Hashtag,
};

class HashtagRepository extends BaseRepository implements HashtagInterface
{
    public $loggedinUser;

    public function __construct(Hashtag $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('hashtag') && $request->hashtag !== null) {
                $query->where('hashtag', $request->hashtag);
            }
            
            if ($request->exists('model_id') && $request->model_id !== null) {
                $query->where('model_id', $request->model_id);
            }

            if ($request->exists('q') && $request->q !== null) {
                $query->where('hashtag', 'like', "%{$request->q}%");
            }
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        if ($request->group_by) {
            $models = $models->groupBy($request->group_by)->select($request->group_by, \DB::raw('count(*) as count'));
        }

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $model = $this->model->create([
            'hashtag' => $request->hashtag,
            'hashtagable_id' => $request->hashtagable_id,
            'model_id' => $request->model_id,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }
}
