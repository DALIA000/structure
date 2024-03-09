<?php

namespace App\Http\Controllers;

use App\Events\SessionLiveSatrted;
use App\Http\Repositories\SessionLive\SessionLiveInterface;
use App\Http\Requests\CreateSessionLiveRequest;
use App\Http\Requests\JoinSessionLiveRequest;
use Illuminate\Http\Request;
use App\Http\Repositories\{
    CourseSession\CourseSessionInterface,
};
use App\Services\ResponseService;
use Carbon\Carbon;

class CourseSessionController extends Controller
{
    public $loggedinUser;

    public function __construct(public CourseSessionInterface $CourseSessionI, public SessionLiveInterface $SessionLiveI, public ResponseService $responseService) {
        $this->loggedinUser = app('loggedinUser');
    }

    public function live(CreateSessionLiveRequest $request, $course_id)
    {
        $request->merge([
            'status_id' => 1,
            'course_id' => $course_id,
            'date_from' => Carbon::now()->format('Y-m-d'),
            'time_from' => Carbon::now()->format('H:i'),
        ]);

        $model = $this->SessionLiveI->create($request);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        
        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        SessionLiveSatrted::dispatch($model['data']['model']);
        return $this->responseService->json('Success!', $model['data'], 200);
    }

    public function join(JoinSessionLiveRequest $request, $session_id)
    {
        $model = $this->SessionLiveI->join($session_id);
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        
        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }
        return $this->responseService->json('Success!', $model['data'], 200);
    }

    public function jwt(Request $request)
    {
        $request->merge([
            'username' => $this->loggedinUser?->username,
            'status_id' => 1,
            'course_id' => $request->course_id,
            'date_from' => Carbon::now()->format('Y-m-d'),
            'time_from' => Carbon::now()->format('H:i'),
        ]);

        $models = $this->CourseSessionI->models($request);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        
        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }
        
        $model = $models['data']?->first();
        
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }

        $models = $this->CourseSessionI->live($model->id);

        $data = $models['data']['jwt_token'];
        return $data;
    }
}
