<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Subscribe\SubscribeInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\ClubPlan\ClubPlanInterface;
use App\Http\Resources\{
    SubscribtionsListResource,
    ClubPlansListResource
};
use App\Services\ResponseService;

class ClubPlanController extends Controller
{
    public $loggedinUser;

    public function __construct(private ClubPlanInterface $ClubPlanI, private SubscribeInterface $SubscribeI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
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

    public function subscribe(Request $request, $id)
    {
        $request->merge([
            'plan_id' => $id,
            'user_id' => $this->loggedinUser?->id,
        ]);

        $plan = $this->SubscribeI->create($request);

        if (!$plan) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$plan['status']) {
            return $this->responseService->json('Fail!', [], 400, $plan['errors']);
        }

        $data = new SubscribtionsListResource($plan['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
