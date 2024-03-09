<?php

namespace App\Http\Repositories\Country;

use App\Http\Repositories\Country\CountryInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Country;

class CountryRepository extends BaseRepository implements CountryInterface
{
    public $loggedinUser;

    public function __construct(Country $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->where(function ($query) use ($request) {
                    $query->where('slug', 'like', '%'.$request->search.'%')
                          ->orWhereHas('locales', function ($query) use ($request) {
                              $query->where('name', 'like', '%'.$request->search.'%');
                          });
                });
            }

            if ($request->exists('slug') && $request->slug !== null) {
                $query->where('slug', $request->slug);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $model = \DB::transaction(function () use ($request) {
            $first_key = array_key_first($request->locales);
            
            $model = $this->model->create([
                'slug' => \Str::slug($request->locales[$first_key]['name']),
                'status' => $request->exists('status') ? $request->status : 1,
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

        if ($request->exists('country') && $request->country !== $model->country_id) {
            $model->country_id = $request->country;
        }

        if ($request->exists('status') && $request->status !== $model->status) {
            $model->status = $request->status;
        }

        if ($request->exists('locales')) {
            $this->setLocales($model, $request->locales, slug:true);
            $model->updated_at = now();
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}
