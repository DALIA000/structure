<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Repositories\User\UserInterface;
use App\Http\Resources\CompetitioneSubscribtionsListResource;
use App\Http\Resources\CourseSubscribtionsListResource;
use App\Http\Resources\PlanSubscribtionsListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class UserSubscribtionController extends Controller
{
    private $loggedinUser;

    public function __construct(
        public UserInterface $UserI, 
        public ResponseService $responseService
    ) {
        $this->loggedinUser = app('loggedinUser');
    }

    public function subscribtions(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'club_member',
            'plan',
            'plan.club'
        ]]);

        $subscriptions = $this->UserI->subscribtions($request);

        if (!$subscriptions) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$subscriptions['status']) {
            return $this->responseService->json('Fail!', [], 400, $subscriptions['data']);
        }

        $data = PlanSubscribtionsListResource::collection($subscriptions['data']);
        return $this->responseService->json('success!', $data, 200);
    }

    public function course_subscribtions(Request $request)
    {
        $subscriptions = $this->UserI->course_subscribtions($request);

        if (!$subscriptions) {
            return $this->responseService->json('Fail!', [], 400, 'notfound');
        }

        if (!$subscriptions['status']) {
            return $this->responseService->json('Fail!', [], 400, $subscriptions['data']);
        }

        $data = CourseSubscribtionsListResource::collection($subscriptions['data']);
        return $this->responseService->json('success!', $data, 200);
    }

    public function competition_subscribtions(Request $request)
    {
        $subscriptions = $this->UserI->competition_subscribtions($request);

        if (!$subscriptions) {
            return $this->responseService->json('Fail!', [], 400, 'notfound');
        }

        if (!$subscriptions['status']) {
            return $this->responseService->json('Fail!', [], 400, $subscriptions['data']);
        }

        $data = CompetitioneSubscribtionsListResource::collection($subscriptions['data']);
        return $this->responseService->json('success!', $data, 200);
    }
}
