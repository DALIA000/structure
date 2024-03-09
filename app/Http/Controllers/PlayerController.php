<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Player\PlayerInterface;
use App\Http\Requests\LinkPlayerRequest;
use App\Http\Resources\{
    PlayersListResource,
    PlayerResource
};
use App\Services\ResponseService;

class PlayerController extends Controller
{
    public $loggedinUser;

    public function __construct(public PlayerInterface $PlayerI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function players(Request $request)
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

        $models = $this->PlayerI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = PlayersListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['players' => $data], 200, paginate: 'players');
    }

    public function player(Request $request, $username)
    {
        $request->merge(['with' => [
            'account',
        ]]);

        $model = $this->PlayerI->findByUsername($username, $request);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => $this->PlayerI?->model])]]);
        }

        $data = new PlayerResource($model);

        return $this->responseService->json('Success!', ['player' => $data], 200);
    }

    public function link(LinkPlayerRequest $request, $username)
    {
        $request->merge(['with' => [
            'user',
        ]]);
        
        $player = $this->PlayerI->findByUsername($username);
        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }
        
        $academy = $this->loggedinUser;
        $model = $this->PlayerI->link($request, $player->user?->id, $academy->user?->id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unlink(Request $request, $username)
    {
        $request->merge(['with' => [
            'user',
        ]]);
        
        $player = $this->PlayerI->findByUsername($username);
        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }
        
        $academy = $this->loggedinUser;
        $model = $this->PlayerI->unlink($request, $player->user?->id, $academy->user?->id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
