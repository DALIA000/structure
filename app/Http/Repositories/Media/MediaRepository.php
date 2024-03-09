<?php

namespace App\Http\Repositories\Media;

use App\Http\Repositories\Media\MediaInterface;
use App\Http\Repositories\Base\BaseRepository;
use App\Http\Resources\MediaResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Services\FileUploader;
use Str;
use DB;
use App\Models\File;
use Ramsey\Uuid\Uuid;

class MediaRepository extends BaseRepository implements MediaInterface
{
    public $loggedinUser;

    public function __construct(Media $model)
    {
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function models($request)
    {
        $models = $this->model->where(function ($query) use ($request) {
        });

        [$sort, $order] = $this->setSortParams($request);
        $models->orderBy($sort, $order);

        $models = $request->per_page ? $models->paginate($request->per_page) : $models->get();

        return ['status' => true, 'data' => $models];
    }

    public function upload($request)
    {
        $media = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Uuid::uuid4()->toString() . '.' . $file->getClientOriginalExtension();
            $media = File::create()->addMedia($file)->usingFileName($fileName)->toMediaCollection('media');
        }

        if (!$media) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }

        $data = new MediaResource($media);
        return ['status' => true, 'data' => $data];
    }
}
