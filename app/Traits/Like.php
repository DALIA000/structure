<?php

namespace App\Traits;


trait Like
{
    public function like($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->likes()->create([
            'user_id' => $this->loggedinUser?->id
        ]);

        return ['status' => true, 'data' => []];
    }

    public function dislike($request, $id)
    {
        $model = $this->findById($id);

        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $model->likes()->where([
            'user_id' => $this->loggedinUser?->id
        ])?->delete();

        return ['status' => true, 'data' => []];
    }
}
