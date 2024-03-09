<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\DeleteAccountRequest\DeleteAccountRequestInterface;
use App\Http\Repositories\User\UserInterface;
use App\Http\Requests\Dashboard\DeleteAccountRequestRequest;
use App\Http\Requests\Dashboard\SuspendAccountRequestRequest;
use App\Http\Resources\Dashboard\DeleteAccountRequestsListResourse;
use App\Http\Resources\Dashboard\UsersListResourse;
use App\Models\DeleteAccountRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(public UserInterface $UsersI, public DeleteAccountRequestInterface $DeleteAccountRequestI, public ResponseService $responseService)
    {
    }

    public function users(Request $request)
    {
        $request->merge([
            'with' => [],
            'withCount' => [
                'followers' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'follows' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'videos' => function ($query) {
                    return $query->where('status_id', 1);
                }
            ]
        ]);

        $model = $this->UsersI->models($request);

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['data']);
        }

        $data = UsersListResourse::collection($model['data']);
        return $this->responseService->json('success', $data, 200);
    }

    public function deleteAccountRequests(Request $request)
    {
        $request->merge([
            'with' => [
                'user'
            ],
            'withCount' => [
                'user.followers' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'user.follows' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'user.videos' => function ($query) {
                    return $query->where('status_id', 1);
                }
            ]
        ]);

        $model = $this->DeleteAccountRequestI->models($request);

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['data']);
        }

        $data = DeleteAccountRequestsListResourse::collection($model['data']);
        return $this->responseService->json('success', $data, 200);
    }

    public function delete(DeleteAccountRequestRequest $request, $id)
    {
        $model = $this->UsersI->deleteAccount($request, $id);

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('success', [], 200);
    }

    public function suspend(SuspendAccountRequestRequest $request, $id)
    {
        $model = $this->UsersI->suspend($request, $id); 

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('success', [], 200);
    }

    public function accept(Request $request, $id)
    {
        $model = $this->UsersI->accept($request, $id); 

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('success', [], 200);
    }
}
