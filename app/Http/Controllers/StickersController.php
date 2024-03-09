<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Stickers\StickerInterface;
use App\Http\Resources\StickersListResource;
use App\Services\ResponseService;

use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class StickersController extends Controller
{
    public function __construct(public StickerInterface $StickerI, public ResponseService $responseService) {
        $this->StickerI = $StickerI;
    }

public function stickers(Request $request)
{
    $stickers = $this->StickerI->get_stickers($request);

    if (!$stickers) {
        return $this->responseService->json('Fail!', [], 400, ['not found']);
    }

    $data = StickersListResource::collection($stickers);
    return $this->responseService->json('success!', $data, 200);
}


}
