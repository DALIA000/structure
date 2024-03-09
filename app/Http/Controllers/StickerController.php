<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Sticker\StickerInterface;
use App\Http\Resources\StickersListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class StickerController extends Controller
{
    public function __construct(public StickerInterface $StickerI, public ResponseService $responseService) 
    {
        $this->StickerI = $StickerI;
    }

    public function stickers(Request $request)
    {
        $stickers = $this->StickerI->models($request);

        if (!$stickers) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$stickers['status']) {
            return $this->responseService->json('Fail!', [], 400, $stickers['data']);
        }

        $data = StickersListResource::collection($stickers['data']);
        return $this->responseService->json('success!', $data, 200);
    }
}
