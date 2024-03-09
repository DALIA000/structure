<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Federation\FederationInterface;
use App\Http\Requests\Dashboard\{
    CreateFederationRequest,
    EditFederationRequest,
};
use App\Http\Resources\Dashboard\{
    FederationsListResource,
    FederationResource,
    FederationOverviewResource,
};
use App\Services\ResponseService;

class FederationController extends Controller
{
    public function __construct(private FederationInterface $FederationI, private ResponseService $responseService)
    {
        $this->FederationI = $FederationI;
    }

    public function federations(Request $request)
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
            'account',
            'status',
            'status.locale',
        ]]);

        $federations = $this->FederationI->models($request);

        if (!$federations) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federations['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federations['errors']]);
        }

        $data = FederationsListResource::collection($federations['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function federation(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ]]);

        $federation = $this->FederationI->findByIdWith($request);

        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new FederationResource($federation);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateFederationRequest $request)
    {
        $federation = $this->FederationI->create($request);

        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federation['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditFederationRequest $request, $id)
    {
        $federation = $this->FederationI->edit($request, $id);

        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federation['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function activate(Request $request, $id)
    {
        $federation = $this->FederationI->activate($request, $id);

        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federation['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $federation = $this->FederationI->block($request, $id);

        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federation['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $federation = $this->FederationI->unblock($request, $id);


        if (!$federation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $federation['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function overview(Request $request, $id)
    {
        $request->merge(['with' => [
            'account' => function ($query) {
                $query->withCount([
                    'videos',
                    'followings',
                    'followers',
                    'tagged_videos'
                ]);
            },
        ], 'withCount' => [
        ]]);

        $academy = $this->FederationI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new FederationOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
