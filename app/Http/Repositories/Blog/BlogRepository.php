<?php

namespace App\Http\Repositories\Blog;

use App\Http\Repositories\Base\BaseRepository;
use App\Http\Repositories\Status\StatusInterface;
use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BlogRepository extends BaseRepository implements BlogInterface
{
    public function __construct(Blog $model, private Media $media, public StatusInterface $StatusI)
    {
        $this->model = $model;
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('search') && $request->search !== null) {
                $query->whereHas('locales', function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%");
                })->orWhereHas('tags', function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%");
                });
            }
            
            if ($request->exists('status_id') && $request->status_id !== null) {
                $query->where('status_id', $request->status_id);
            }
        });

        if ($request->exists('trashed') && $request->trashed !== null) {
            $models->onlyTrashed();
        }

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);
        
        switch ($request->sort) {
            default:
                $sort = $request->sort ?: 'created_at';
                break;
        }

        switch ($request->order) {
            default:
                $order = $request->order ?: 'desc';
                break;
        }


        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        $model = DB::transaction(function () use ($request) {
            $model = $this->model->create([
                'status' => $request->status ?: 1,
            ]);

            if ($request->locales) {
                setLocales($this->model, $request->locales, $model->id);
            }

            $model->tags()->attach($request->tags);

            $this->media::where('id', $request->media)->update([
                'model_id' => $model->id,
                'model_type' => get_class($this->model),
            ]);

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function update($request, $id)
    {
        $model = DB::transaction(function () use ($request, $id) {
            $model = $this->model->find($id);

            if ($request->status) {
                $model->update(["status" => $request->status]);
            }
            if ($request->locales) {
                setLocales($this->model, $request->locales, $model->id);
            }
            if ($request->tags) {
                $model->tags()->sync($request->tags);
            }

            if ($request->media) {
                $model->clearMediaCollection('media');
                $this->media::where('id', $request->media)->update([
                    'model_id' => $model->id,
                    'model_type' => get_class($this->model),
                ]);
            }

            return $model;
        });

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        return ['status' => true, 'data' => $model];
    }

    public function activate($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('active')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function archive($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $status_id = $this->StatusI->findBySlug('archived')?->id;
        $this->setStatus($model, $status_id);

        return ['status' => true, 'data' => $model?->account];
    }

    public function setStatus($model, $status)
    {
        return $model->update([
            'status_id' => $status,
        ]);
    }
}
