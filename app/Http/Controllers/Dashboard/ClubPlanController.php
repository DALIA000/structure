<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\ClubPlan\ClubPlanInterface;
use App\Http\Requests\Dashboard\{
    CreateClubPlanRequest,
    UpdateClubPlanRequest,
};
use App\Http\Resources\Dashboard\{
    ClubPlansListResource,
};
use App\Services\ResponseService;

class ClubPlanController extends Controller
{
    public function __construct(private ClubPlanInterface $ClubPlanI, private ResponseService $responseService)
    {
        $this->ClubPlanI = $ClubPlanI;
    }

    public function plans(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        $request->merge(['with' => [
            'club_plan_type',
            'club_plan_type.locale',
        ], 'withCount' => [
            'subscribers', // current
            'subscribtions' // all
        ]]);

        $plans = $this->ClubPlanI->models($request);

        if (!$plans) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$plans['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $plans['errors']]);
        }

        $data = ClubPlansListResource::collection($plans['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateClubPlanRequest $request)
    {
        $plan = $this->ClubPlanI->create($request);

        if (!$plan) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$plan['status']) {
            return $this->responseService->json('Fail!', [], 400, $plan['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(UpdateClubPlanRequest $request)
    {
        $plans = $this->ClubPlanI->update($request);

        if (!$plans) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$plans['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $plans['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
