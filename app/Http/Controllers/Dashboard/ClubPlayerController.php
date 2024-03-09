<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\ClubPlayer\ClubPlayerInterface;
use App\Http\Resources\Dashboard\{
    ClubPlayersListResource,
    ClubPlayerResource,
};
use App\Http\Requests\Dashboard\{
    CreateClubPlayerRequest,
    EditClubPlayerRequest,
    DeleteClubPlayerRequest,
};
use App\Services\ResponseService;

class ClubPlayerController extends Controller
{
    public function __construct(private ClubPlayerInterface $ClubPlayerI, private ResponseService $responseService)
    {
        $this->ClubPlayerI = $ClubPlayerI;
    }

    public function club_players(Request $request)
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
            'club',
            'player_position',
            'player_position.locale',
        ]]);

        $players = $this->ClubPlayerI->models($request);

        if (!$players) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$players['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $players['errors']]);
        }

        $data = ClubPlayersListResource::collection($players['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function club_player(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'club',
            'player_position',
            'player_position.locale',
        ]]);

        $player = $this->ClubPlayerI->findByIdWith($request);

        if (!$player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new ClubPlayerResource($player);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateClubPlayerRequest $request)
    {
        $club_player = $this->ClubPlayerI->create($request);

        if (!$club_player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_player['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club_player['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditClubPlayerRequest $request, $id)
    {
        $club_player = $this->ClubPlayerI->edit($request, $id);

        if (!$club_player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_player['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club_player['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->ClubPlayerI->forceDelete($id);
        } else {
            $deleted = $this->ClubPlayerI->delete($id);
        }
        if (!$deleted) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        if (!$deleted['status']) {
            return $this->responseService->json('Fail!', [], 400, $deleted['errors']);
        }
        return $this->responseService->json('Success!', [], 200);
    }
}
