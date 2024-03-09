<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\UserType\UserTypeInterface;
use App\Http\Requests\Dashboard\{
    CreateUserTypeRequest,
    EditUserTypeRequest,
};
use App\Http\Resources\Dashboard\{
    UserTypeResource,
};
use App\Services\ResponseService;

class UserTypeController extends Controller
{
    public function __construct(private UserTypeInterface $UserTypeI, private ResponseService $responseService)
    {
        $this->UserTypeI = $UserTypeI;
    }

    public function user_types(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'created_at']);
        }

        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $user_types = $this->UserTypeI->models($request);

        if (!$user_types) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$user_types['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $user_types['errors']]);
        }

        $data = UserTypeResource::collection($user_types['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
