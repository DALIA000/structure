<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Resources\Dashboard\ClubOverviewResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Club\ClubInterface;
use App\Http\Requests\Dashboard\{
    CreateClubRequest,
    EditClubRequest,
};
use App\Http\Resources\Dashboard\{
    ClubsListResource,
    ClubResource,
};
use App\Services\ResponseService;

class ClubController extends Controller
{
    public function __construct(private ClubInterface $ClubI, private ResponseService $responseService)
    {
        $this->ClubI = $ClubI;
    }

    public function clubs(Request $request)
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
            'account',
            'status',
            'status.locale',
            'account.city',
            'account.city.locale',
        ]]);

        $clubs = $this->ClubI->models($request);

        if (!$clubs) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$clubs['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $clubs['errors']]);
        }

        $data = ClubsListResource::collection($clubs['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function club(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
            'club_players',
            'subscribers',
            'subscribtions',
        ]]);

        $club = $this->ClubI->findByIdWith($request);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new ClubResource($club);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateClubRequest $request)
    {
        $club = $this->ClubI->create($request);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditClubRequest $request, $id)
    {
        $club = $this->ClubI->edit($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function activate(Request $request, $id)
    {
        $club = $this->ClubI->activate($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $club = $this->ClubI->block($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $club = $this->ClubI->unblock($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
    public function overview(Request $request, $id){
        $request->merge(['with' => [
            'account' => function ($query) {
                $query->withCount([
                    'videos',
                    'followings',
                    'followers'
                ]);
            },
        ], 'withCount' => [
            'competitions',
        ]]);

        $business = $this->ClubI->findByIdWith($request);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new ClubOverviewResource($business);
        return $this->responseService->json('Success!', $data, 200);
    }
}
