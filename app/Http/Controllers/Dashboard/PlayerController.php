<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Player\PlayerInterface;
use App\Http\Resources\Dashboard\{
    PlayersListResource,
    PlayerResource,
    PlayerOvervievResource,
};
use App\Http\Requests\Dashboard\{
    AcceptUserRequest,
    RejectUserRequest,
};
use App\Services\ResponseService;

class PlayerController extends Controller
{
    public function __construct(private PlayerInterface $PlayerI, private ResponseService $responseService)
    {
        $this->PlayerI = $PlayerI;
    }

    public function players(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'academy_player',
            'academy_player.academy',
            'status.locale',
            'player_position',
            'player_position.locale',
            'player_footness',
            'player_footness.locale',
        ]]);

        $players = $this->PlayerI->models($request);

        if (!$players) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$players['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $players['errors']]);
        }

        $data = PlayersListResource::collection($players['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function player(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }
        $request->merge(['with' => [
            'status',
            'academy_player',
            'academy_player.academy',
            'status.locale',
            'player_position',
            'player_position.locale',
            'player_footness',
            'player_footness.locale',
        ]]);

        $player = $this->PlayerI->findByIdWith($request);

        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new PlayerResource($player);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $academy = $this->PlayerI->accept($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $academy = $this->PlayerI->reject($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $player = $this->PlayerI->block($request, $id);

        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$player['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $player['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $player = $this->PlayerI->unblock($request, $id);

        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$player['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $player['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function overview(Request $request, $id)
    {
        $request->merge(['with' => [
            'account' => function ($query) {
                $query->withCount([
                    'videos',
                    'followings',
                    'followers',
                    'tagged_videos'
                ]);
            },
        ], 'withCount' => [
        ]]);

        $academy = $this->PlayerI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new PlayerOvervievResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
