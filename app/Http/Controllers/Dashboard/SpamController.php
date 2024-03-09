<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Spam\SpamInterface;
use App\Http\Requests\Dashboard\{
    CreateSpamRequest,
    EditSpamRequest,
};
use App\Http\Resources\Dashboard\{
    SpamsListResource,
    SpamResource,
};
use App\Services\ResponseService;

class SpamController extends Controller
{
    public function __construct(private SpamInterface $SpamI, private ResponseService $responseService)
    {
        $this->SpamI = $SpamI;
    }

    public function spams(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $spams = $this->SpamI->models($request);

        if (!$spams) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spams['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $spams['errors']]);
        }

        $data = SpamsListResource::collection($spams['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function spam(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'locales',
        ]]);

        $spam = $this->SpamI->findByIdWith($request);

        if (!$spam) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new SpamResource($spam);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateSpamRequest $request)
    {
        $spam = $this->SpamI->create($request);

        if (!$spam) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spam['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $spam['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditSpamRequest $request, $id)
    {
        $spam = $this->SpamI->edit($request, $id);

        if (!$spam) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spam['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $spam['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->SpamI->forceDelete($id);
        } else {
            $deleted = $this->SpamI->delete($id);
        }
        if (!$deleted) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        if (!$deleted['status']) {
            return $this->responseService->json('Fail!', [], 400, $deleted['errors']);
        }
        return $this->responseService->json('Success!', [], 200);
    }
}
