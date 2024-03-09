<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\DeleteAccountRequest\DeleteAccountRequestInterface;
use App\Http\Requests\Dashboard\DeleteAccountRequestRequest;
use App\Http\Resources\Dashboard\DeleteAccountRequestsListResourse;
use App\Models\DeleteAccountRequest;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class DeleteAccountRequestController extends Controller
{
    public function __construct(public DeleteAccountRequestInterface $DeleteAccountRequestI, public ResponseService $responseService)
    {
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

    public function read(Request $request, $id)
    {
        $model = $this->DeleteAccountRequestI->set_status($id, 1);

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['data']);
        }

        return $this->responseService->json('success', [], 200);
    }

    public function unread(Request $request, $id)
    {
        $model = $this->DeleteAccountRequestI->set_status($id, 0);

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['data']);
        }

        return $this->responseService->json('success', [], 200);
    }
}
