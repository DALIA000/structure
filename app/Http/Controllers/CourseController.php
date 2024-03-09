<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\{
    Course\CourseInterface,
};
use App\Services\ResponseService;
use App\Http\Requests\{
    CreateCourseRequest,
    UpdateCourseRequest,
    SubscribeCourseRequest
};
use App\Http\Resources\{
    CoursesListResource,
    CourseResource
};
use App\Models\{
    UserType,
    Academy
};

class CourseController extends Controller
{
    public $loggedinUser;

    public function __construct(public CourseInterface $CourseI, public ResponseService $responseService) {
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

        $request->merge([
            'username' => $request->trainer,
            'status_id' => 1,
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
        ], 'withCount' => [
            'sessions',
            // 'subscribtions',
        ]]);

        $models = $this->CourseI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CoursesListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['courses' => $data], 200, paginate: 'courses');
    }

    public function course(Request $request, $id)
    {
        $request->merge([
            'video_id' => $id,
            'status_id' => 1,
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
        
        $data = new CourseResource($model);

        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateCourseRequest $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $request->merge([
            'user_id' => $user->id,
        ]);

        $course = $this->CourseI->create($request);

        if (!$course) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$course['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $course['errors']]);
        }

        $data = $course['data'];
        return $this->responseService->json('Success!', $data->video?->only('id'), 200);
    }

    public function update(UpdateCourseRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        $course = $this->CourseI->update($request, $id);

        if (!$course) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$course['status']) {
            return $this->responseService->json('Fail!', [], 400, $course['errors']);
        }

        $data = $course['data'];
        return $this->responseService->json('Success!', $data->video?->only('id'), 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $course = $this->CourseI->course_delete($request, $id);

        if (!$course) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$course['status']) {
            return $this->responseService->json('Fail!', [], 400, $course['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function subscribe(SubscribeCourseRequest $request, $id)
    {
        if ($this->loggedinUser?->user_type_class == Academy::class) {
            $subscribe = $this->CourseI->academySubscribe($request, $id);
        } else {
            $subscribe = $this->CourseI->subscribe($request, $id);
        }

        if (!$subscribe) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$subscribe['status']) {
            return $this->responseService->json('Fail!', [], 400, $subscribe['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function invoice(Request $request, $id)
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
}
