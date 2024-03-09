<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\{
    Academy\AcademyInterface,
    AcademyPlayer\AcademyPlayerInterface
};
use App\Http\Resources\{
    AcademiesListResource,
    AcademyResource,
    AcademyPlayerResource
};
use App\Services\ResponseService;
use Illuminate\Support\Facades\Gate;

class AcademyController extends Controller
{
    public $loggedinUser;

    public function __construct(public AcademyInterface $AcademyI, public AcademyPlayerInterface $AcademyPlayerI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function academies(Request $request)
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

        $models = $this->AcademyI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = AcademiesListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['academies' => $data], 200, paginate: 'academies');
    }

    public function academy(Request $request, $username)
    {
        $request->merge(['with' => [
            'user',
        ]]);

        $model = $this->AcademyI->findByUsername($username, $request);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->AcademyI?->model])]]);
        }

        $data = new AcademyResource($model);

        return $this->responseService->json('Success!', ['academy' => $data], 200);
    }

    public function link(Request $request, $username)
    {
        $request->merge(['with' => [
            'user',
        ]]);

        $academy = $this->AcademyI->findByUsername($username);
        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }

        $player = $this->loggedinUser;
        $model = $this->AcademyI->request_link($request, $player->user?->id, $academy->user?->id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function link_requests(Request $request)
    {
        $academy = $this->loggedinUser;
        $request->merge(['status' => 'pending', 'academy_id' => $academy->user->id, 'with' => [
            'player',
        ]]);

        $models = $this->AcademyPlayerI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = AcademyPlayerResource::collection($models['data']);

        return $this->responseService->json('Success!', $data, 200);
    }
}
