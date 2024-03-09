<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\{
    Federation\FederationInterface,
};
use App\Http\Resources\{
    FederationsListResource,
    FederationResource,
};
use App\Services\ResponseService;

class FederationController extends Controller
{
    public $loggedinUser;

    public function __construct(public FederationInterface $FederationI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function federations(Request $request)
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

        $models = $this->FederationI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = FederationsListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['federations' => $data], 200, paginate: 'federations');
    }

    public function federation(Request $request, $username)
    {
        $request->merge(['with' => [
            'user',
        ]]);

        $model = $this->FederationI->findByUsername($username, $request);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->FederationI?->model])]]);
        }

        $data = new FederationResource($model);

        return $this->responseService->json('Success!', ['federation' => $data], 200);
    }
}
