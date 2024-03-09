<?php
namespace App\Http\Repositories\Sticker;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\Sticker;
use App\Services\FileUploader;
use App\Services\ResponseService;

class StickerRepository extends BaseRepository implements StickerInterface
{
    public function __construct(Sticker $model, public ResponseService $responseService){
        $this->model = $model;
    }

    public function models($request)
    {
        $model = $this->model->orderBy('created_at', 'desc')->get();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }
        return ['status' => true, 'data' => $model];
    }

    public function create($request)
    {
        if ($request->hasFile('sticker')) {
            $sticker = FileUploader::uploadSingle($request->file('sticker'), 'stickers');
        }

        $model = $this->model::create([
            'sticker' => $sticker['document'],
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }
        return ['status' => true, 'data' => $model];
    }
}
