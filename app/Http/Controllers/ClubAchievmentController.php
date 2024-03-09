<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ClubAchievment\ClubAchievmentInterface;
use App\Http\Resources\{
    ClubAchievmentsListResource,
};
use App\Services\ResponseService;
use App\Http\Requests\{
    CreateClubAchievmentRequest,
    EditClubAchievmentRequest,
};

class ClubAchievmentController extends Controller
{
    public $loggedinUser;

    public function __construct(public ClubAchievmentInterface $ClubAchievmentI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function achievments(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'club' => $request->username,
        ]);

        $club_achievments = $this->ClubAchievmentI->models($request);

        if (!$club_achievments) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_achievments['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_achievments['errors']);
        }

        $data = ClubAchievmentsListResource::collection($club_achievments['data']);

        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateClubAchievmentRequest $request)
    {
        $club_achievment = $this->ClubAchievmentI->create($request, $this->loggedinUser?->user->id);

        if (!$club_achievment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_achievment['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_achievment['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function update(EditClubAchievmentRequest $request, $id)
    {
        $request->merge([
            'club_id' => $this->loggedinUser?->user?->id,
        ]);

        $club_achievment = $this->ClubAchievmentI->edit($request, $id);

        if (!$club_achievment) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_achievment['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_achievment['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $course = $this->ClubAchievmentI->achievment_delete($request, $id);

        if (!$course) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$course['status']) {
            return $this->responseService->json('Fail!', [], 400, $course['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
