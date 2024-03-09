<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\TrainerExperienceLevel\TrainerExperienceLevelInterface;
use App\Http\Resources\TrainerExperienceLevelResource;
use App\Services\ResponseService;

class TrainerExperienceLevelController extends Controller
{
  public function __construct(public TrainerExperienceLevelInterface $TrainerExperienceLevelI, public ResponseService $responseService)
  {
  }

  public function trainer_experience_levels(Request $request)
  {
    if (!$request->exists('order') || $request->order === null) {
        $request->merge(['order' => 'asc']);
    }

    if (!$request->exists('sort') || $request->sort === null) {
        $request->merge(['sort' => 'created_at']);
    }
    
    $request->merge([
      'with' => 'locale'
    ]);

    $models = $this->TrainerExperienceLevelI->models($request);
    if (!$models) {
      return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
    }

    if (!$models['status']) {
      return $this->responseService->json('Fail!', [], 400, $models['errors']);
    }

    $data = TrainerExperienceLevelResource::collection($models['data']);

    return $this->responseService->json('Success!', ['trainer_experience_levels' => $data], 200, paginate: 'trainer_experience_levels');
  }
}
