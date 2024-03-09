<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\UserType\UserTypeInterface;
use App\Http\Resources\UserTypeResource;
use App\Services\ResponseService;

class UserTypeController extends Controller
{
  public function __construct(public UserTypeInterface $UserTypeI, public ResponseService $responseService)
  {
  }

  public function user_types(Request $request)
  {
    if (!$request->exists('order') || $request->order === null) {
        $request->merge(['order' => 'asc']);
    }

    if (!$request->exists('sort') || $request->sort === null) {
        $request->merge(['sort' => 'created_at']);
    }
    
    $request->merge([
      'with' => 'locale',
      'available' => 1,
    ]);

    $models = $this->UserTypeI->models($request);
    if (!$models) {
      return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
    }

    if (!$models['status']) {
      return $this->responseService->json('Fail!', [], 400, $models['errors']);
    }

    $data = UserTypeResource::collection($models['data']);

    return $this->responseService->json('Success!', ['user_types' => $data], 200, paginate: 'user_types');
  }
}
