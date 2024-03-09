<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ClubPlayer\ClubPlayerInterface;
use App\Http\Resources\{
    ClubPlayersListResource,
};
use App\Services\ResponseService;
use App\Http\Requests\{
    CreateClubPlayerRequest,
    EditClubPlayerRequest,
};

class ClubPlayerController extends Controller
{
    public $loggedinUser;

    public function __construct(public ClubPlayerInterface $ClubPlayerI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function clubPlayers(Request $request)
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

    public function create(CreateClubPlayerRequest $request)
    {
        $request->merge([
            'club' => $this->loggedinUser?->user->id
        ]);

        $club_player = $this->ClubPlayerI->create($request);

        if (!$club_player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_player['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club_player['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function update(EditClubPlayerRequest $request, $id)
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
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $club_player = $this->ClubPlayerI->club_player_delete($request, $id);

        if (!$club_player) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_player['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_player['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
