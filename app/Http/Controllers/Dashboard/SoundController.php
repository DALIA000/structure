<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Sound\SoundInterface;
use App\Http\Requests\Dashboard\{
    CreateSoundRequest,
    EditSoundRequest
};
use App\Http\Resources\Dashboard\{
    SoundsListResource,
    SoundResource
};
use App\Services\ResponseService;
use Illuminate\Http\Request;

class SoundController extends Controller
{
    public function __construct(public SoundInterface $SoundI, public ResponseService $responseService)
    {
    }

    public function sounds(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        $sounds = $this->SoundI->models($request);

        if (!$sounds) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sounds['data']) {
            return $this->responseService->json('Fail!', [], 400, $sounds['errors']);
        }

        $data = SoundsListResource::collection($sounds['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function sound(Request $request, $id)
    {
        $sound = $this->SoundI->findByIdWith($request);

        if (!$sound) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound')]]);
        }

        $data = new SoundResource($sound);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateSoundRequest $request)
    {
        $sound = $this->SoundI->create($request);

        if (!$sound) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sound['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $sound['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditSoundRequest $request, $id)
    {
        $sound = $this->SoundI->update($request, $id);

        if (!$sound) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sound['status']) {
            return $this->responseService->json('Fail!', [], 400, $sound['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $sound = $this->SoundI->delete($id);

        if (!$sound) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$sound['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $sound['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
