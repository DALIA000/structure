<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\ChangeUserTypeRequest\ChangeUserTypeRequestInterface;
use App\Http\Repositories\User\UserInterface;
use App\Http\Resources\Dashboard\UsersListResourse;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class ChangeUserTypeRequestController extends Controller
{
    public function __construct(public UserInterface $UsersI, public ChangeUserTypeRequestInterface $ChangeUserTypeRequestI, public ResponseService $responseService)
    {
    }

    public function accept(Request $request, $id)
    {
        $request->merge([
            'user_type_request_id' => $id
        ]);
        $user_type_request = $this->ChangeUserTypeRequestI->findById($id); 
        if(!$user_type_request){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        $model = $this->UsersI->ChangeUserType($request, $user_type_request->user_id); 

        if(!$model){
           return $this->responseService->json('fail!', [], 400, ['error' => [trans('message.error')]]);
        }

        if(!$model['status']){
           return $this->responseService->json('fail!', [], 400, $model['errors']);
        }

        return $this->responseService->json('success', [], 200);
    }
}
