<?php
namespace App\Http\Repositories\DeleteAccountRequest;
use App\Http\Repositories\Base\BaseRepository;
use App\Models\DeleteAccountRequest;
use App\Services\FileUploader;
use App\Services\ResponseService;

class DeleteAccountRequestRepository extends BaseRepository implements DeleteAccountRequestInterface
{
    public function __construct(DeleteAccountRequest $model, public ResponseService $responseService){
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
        $model = $this->model::create([
            'user_id' => $request->user_id,
            'full_name' => $request->full_name,
            'user_type' => $request->user_type,
            'followings_count' => $request->followings_count,
            'followers_count' => $request->followers_count,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.create', ['model' => trans_class_basename($this->model)])]]];
        }
        return ['status' => true, 'data' => $model];
    }

    public function set_status($id, $status)
    {
        $model = $this->model->where('id', $id)->first();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $model = $model->update([
            'status_id' => $status,
        ]);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.update', ['model' => trans_class_basename($this->model)])]]];
        }
        return ['status' => true, 'data' => $model];
    }
}
