<?php

namespace App\Http\Repositories\Sound;

use App\Http\Repositories\Sound\SoundInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Sound;
use App\Services\FileUploader;
use App\Services\ResponseService;

class SoundRepository extends BaseRepository implements SoundInterface
{
    public $loggedinUser;

    public function __construct(Sound $model, public ResponseService $responseService)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request){
        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('singer') && $request->singer !== null) {
                $query->where('singer', 'like', "%{$request->singer}%");
            }
        });

        $models = $this->model->where(function ($query) use ($request) {
            if ($request->exists('title') && $request->title !== null) {
                $query->where('title', 'like', "%{$request->title}%");
            }
        });

        $models = $models->with($request->with ?: [])->withCount($request->withCount ?: []);

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function create($request)
    {
        if ($request->hasFile('sound')) {
            $sound = FileUploader::uploadSingle($request->file('sound'), 'sounds');
        }

        if ($request->hasFile('image')) {
            $image = FileUploader::uploadSingle($request->file('image'), 'images');
        }
        $model = $this->model->create([
            'title' => $request->title,
            'singer' => $request->singer,
            'sound' => $sound['document'] ?? null,
            'size' => $sound['size'] ?? null,
            'length' => FileUploader::getduration($sound['document']),
            'image' => $image['document'] ?? null,
        ]);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }
        return ['status' => true, 'data' => $model];
    }

    public function update($request, $id)
    {
        $model = $this->findById($id);
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        if ($request->hasFile('sound') && $request->file('sound')->isValid()) {
            $sound = FileUploader::uploadSingle($request->file('sound'), 'sounds');
            $model->sound = $sound['document'];
        }

        if ($request->hasFile('image')) {
            $image = FileUploader::uploadSingle($request->file('image'), 'images');
            $model->image = $image['document'];
        }

        if ($request->has('singer') && $request->singer !== null) {
            $model->singer = $request->singer;
        }

        if ($request->has('title') && $request->title !== null) {
            $model->title = $request->title;
        }

        $model->save();

        return ['status' => true, 'data' => $model];
    }
}

