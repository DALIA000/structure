<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\AcademyLevel\AcademyLevelInterface;
use App\Http\Resources\AcademyLevelResource;
use App\Services\ResponseService;

class AcademyLevelController extends Controller
{
  public function __construct(public AcademyLevelInterface $AcademyLevelI, public ResponseService $responseService)
  {
  }

  public function academy_levels(Request $request)
  {
    $request->merge([
      'with' => 'locale'
    ]);

    $models = $this->AcademyLevelI->models($request);
    if (!$models) {
      return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
    }

    if (!$models['status']) {
      return $this->responseService->json('Fail!', [], 400, $models['errors']);
    }

    $data = AcademyLevelResource::collection($models['data']);

    return $this->responseService->json('Success!', ['academy_levels' => $data], 200, paginate: 'academy_levels');
  }
}
