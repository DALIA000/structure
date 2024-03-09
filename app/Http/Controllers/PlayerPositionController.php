<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\PlayerPosition\PlayerPositionInterface;
use App\Http\Resources\PlayerPositionResource;
use App\Services\ResponseService;

class PlayerPositionController extends Controller
{
  public function __construct(public PlayerPositionInterface $PlayerPositionI, public ResponseService $responseService)
  {
  }

  public function player_positions(Request $request)
  {
    $request->merge([
      'with' => 'locale'
    ]);

    $models = $this->PlayerPositionI->models($request);
    if (!$models) {
      return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
    }

    if (!$models['status']) {
      return $this->responseService->json('Fail!', [], 400, $models['errors']);
    }

    $data = PlayerPositionResource::collection($models['data']);

    return $this->responseService->json('Success!', ['player_positions' => $data], 200, paginate: 'player_positions');
  }
}
