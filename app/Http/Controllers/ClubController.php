<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClubSubscribersListResource;
use Illuminate\Http\Request;
use App\Http\Repositories\Club\ClubInterface;
use App\Http\Resources\ClubsListResource;
use App\Http\Resources\ClubResource;
use App\Services\ResponseService;

class ClubController extends Controller
{
    public $loggedinUser;
    public function __construct(public ClubInterface $ClubI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function clubs(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'account',
        ]]);

        $models = $this->ClubI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = ClubsListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['clubs' => $data], 200, paginate: 'clubs');
    }

    public function club(Request $request, $username)
    {
        $request->merge([
            'username' => $username
        ]);

        $request->merge([
            'withCount' => ['competitions']
        ]);

        $models = $this->ClubI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $model = $models['data']->first();
        $data = new ClubResource($model);

        return $this->responseService->json('Success!', ['club' => $data], 200);
    }

       public function subscribers(Request $request)
    {
        $request->merge([
            'id' => $this->loggedinUser->id,
        ]);

        $request->merge(['with' => [
            'subscribers'
        ], 'withCount' => [
        ]]);

        $models = $this->ClubI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $model = $models['data']->first();

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->ClubI->model)])]]);
        }

        $data = ClubSubscribersListResource::collection($model->subscribers);

        return $this->responseService->json('Success!', [$data], 200);
    }
}
