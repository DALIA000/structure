<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Course\CourseRepository;
use App\Http\Requests\Dashboard\AcceptCourseRequest;
use App\Http\Resources\Dashboard\CoursesListResource;
use App\Http\Resources\Dashboard\CourseResource;
use App\Http\Resources\Dashboard\CourseSubscribtionsListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use \App\Events\UserRejected;
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Models\{
    Course,
    Model,
};

class CourseController extends Controller
{
    public $CourseI;
    public $loggedinUser;

    public function __construct(CourseRepository $CourseI, public ResponseService $responseService){
        $this->CourseI = $CourseI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function courses(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'video',
            'video.user',
        ], 'withCount' => [
            'sessions',
        ]]);

        $models = $this->CourseI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CoursesListResource::collection($models['data']);

        return $this->responseService->json('Success!', $data, 200);
    }

    public function course(Request $request, $id)
    {
        $request->merge([
            'video_id' => $id,
        ]);

        $request->merge(['with' => [
            'video' => function ($query) {
                $query->withCount([
                    'likes',
                    'comments',
                    'views',
                    'shares',
                ]);
            },
            'video.user',
            'sessions',
            'subscribtions'
        ], 'withCount' => [
            'sessions',
            'subscribtions',
        ]]);

        $models = $this->CourseI->models($request);
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

        $models = $this->CourseI->models($request);
        $data = new CourseResource($model);

        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(AcceptCourseRequest $request, $id)
    {
        $request->merge([
            'invoicable_type' => Course::class,
        ]);

        $invoice = $this->CourseI->accept($request, $id);
        if (!$invoice) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$invoice['status']) {
            return $this->responseService->json('Fail!', [], 400, $invoice['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $model = $this->CourseI->course_delete($request, $id);

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$model['status']) {
            return $this->responseService->json('Fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

     public function reject(RejectUserRequest $request, $id)
    {
        $academy = $this->CourseI->reject($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }
        return $this->responseService->json('Success!', [], 200);
    }

    public function subscribtions(Request $request, $id)
    {
        $models = $this->CourseI->subscribtions($request, $id);

        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CourseSubscribtionsListResource::collection($models['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
