<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ClubPresident\ClubPresidentInterface;
use App\Http\Resources\{
    ClubPresidentsListResource,
};
use App\Services\ResponseService;
use App\Http\Requests\{
    CreateClubPresidentRequest,
    EditClubPresidentRequest,
};

class ClubPresidentController extends Controller
{
    public $loggedinUser;

    public function __construct(public ClubPresidentInterface $ClubPresidentI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function create(CreateClubPresidentRequest $request)
    {
        $club_president = $this->ClubPresidentI->create($request, $this->loggedinUser?->user->id);

        if (!$club_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function update(EditClubPresidentRequest $request)
    {
        $club_president = $this->ClubPresidentI->edit($request, $this->loggedinUser?->user->id);

        if (!$club_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
