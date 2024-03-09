<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\PlayerFootness\PlayerFootnessInterface;
use App\Http\Resources\PlayerFootnessResource;
use App\Services\ResponseService;

class PlayerFootnessController extends Controller
{
  public function __construct(public PlayerFootnessInterface $PlayerFootnessI, public ResponseService $responseService)
  {
  }

  public function player_footnesses(Request $request)
  {
    $request->merge([
      'with' => 'locale'
    ]);

    $models = $this->PlayerFootnessI->models($request);
    if (!$models) {
      return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
    }

    if (!$models['status']) {
      return $this->responseService->json('Fail!', [], 400, $models['errors']);
    }

    $data = PlayerFootnessResource::collection($models['data']);

    return $this->responseService->json('Success!', ['player_footnesses' => $data], 200, paginate: 'player_footnesses');
  }
}
