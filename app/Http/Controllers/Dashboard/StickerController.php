<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Sticker\StickerInterface;
use App\Http\Requests\Dashboard\CreateStickerRequest;
use App\Http\Resources\Dashboard\StickersListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class StickerController extends Controller
{
    public function __construct(public StickerInterface $StickerI, public ResponseService $responseService) {
        $this->StickerI = $StickerI;
    }

    public function stickers(Request $request)
    {
        $stickers = $this->StickerI->models($request);

        if (!$stickers) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$stickers['status']) {
            return $this->responseService->json('Fail!', [], 400, $stickers['data']);
        }

        $data = StickersListResource::collection($stickers['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateStickerRequest $request)
    {
        $sticker = $this->StickerI->create($request);

        if (!$sticker) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sticker['status']) {
            return $this->responseService->json('Fail!', [], 400, $sticker['data']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $sticker = $this->StickerI->delete($id);

        if (!$sticker) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sticker['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $sticker['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
