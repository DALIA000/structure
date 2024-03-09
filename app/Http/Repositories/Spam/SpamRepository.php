<?php

namespace App\Http\Repositories\Spam;

use App\Http\Repositories\Spam\SpamInterface;
use App\Http\Repositories\{
    Base\BaseRepository,
};
use App\Models\{
    Spam,
};
use DB;

class SpamRepository extends BaseRepository implements SpamInterface
{
    public $loggedinUser;

    public function __construct(Spam $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('locales', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->search.'%');
                    });
                });
            }

            if ($request->exists('spam_section_id') && $request->spam_section_id !== null) {
                $query->where('spam_section_id', $request->spam_section_id);
            }

            if ($request->exists('spam_section') && $request->spam_section !== null) {
                $query->whereHas('spam_section', function ($query) use ($request) {
                    $query->where('slug', $request->spam_section);
                });
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

    public function create($request)
    {
        $model = DB::transaction(function () use ($request) {
            $model = $this->model->create([
                'spam_section_id' => $request->spam_section,
            ]);

            if ($request->exists('locales')) {
                $this->setLocales($model, $request->locales);
            }

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

        if ($request->exists('locales')) {
            $this->setLocales($model, $request->locales);
            $model->updated_at = now();
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
