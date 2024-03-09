<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\FederationPresident\FederationPresidentInterface;
use App\Http\Resources\Dashboard\{
    FederationPresidentResource,
};
use App\Http\Requests\Dashboard\{
    CreateFederationPresidentRequest,
    EditFederationPresidentRequest,
};
use App\Services\ResponseService;
use App\Models\FederationPresident;

class FederationPresidentController extends Controller
{
    public function __construct(private FederationPresidentInterface $FederationPresidentI, private ResponseService $responseService, private FederationPresident $model)
    {
        $this->FederationPresidentI = $FederationPresidentI;
    }

    public function federation_president(Request $request, $id)
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

        $request->merge([
            'federation_id' => $id,
        ]);

        $request->merge(['with' => [
        ]]);

        $federation_presidents = $this->FederationPresidentI->models($request);

        if (!$federation_presidents) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation_presidents['data']) {
            return $this->responseService->json('Fail!', [], 400, $federation_presidents['errors']);
        }

        if (!$federation_presidents['data']->count()) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]);
        }

        $data = new FederationPresidentResource($federation_presidents['data']->first());
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateFederationPresidentRequest $request, $id)
    {
        $federation_president = $this->FederationPresidentI->create($request, $id);

        if (!$federation_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $federation_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditFederationPresidentRequest $request, $id)
    {
        $federation_president = $this->FederationPresidentI->edit($request, $id);

        if (!$federation_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$federation_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $federation_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $model = $this->model->where('federation_id', $id)->first();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $id = $model->id;

        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->FederationPresidentI->forceDelete($id);
        } else {
            $deleted = $this->FederationPresidentI->delete($id);
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