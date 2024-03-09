<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\ClubPresident\ClubPresidentInterface;
use App\Http\Resources\Dashboard\{
    ClubPresidentsListResource,
    ClubPresidentResource,
};
use App\Http\Requests\Dashboard\{
    CreateClubPresidentRequest,
    EditClubPresidentRequest,
    DeleteClubPresidentRequest,
};
use App\Services\ResponseService;
use App\Models\ClubPresident;

class ClubPresidentController extends Controller
{
    public function __construct(private ClubPresidentInterface $ClubPresidentI, private ResponseService $responseService, private ClubPresident $model)
    {
        $this->ClubPresidentI = $ClubPresidentI;
    }

    public function club_president(Request $request, $id)
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
            'club_id' => $id,
        ]);

        $request->merge(['with' => [
        ]]);

        $players = $this->ClubPresidentI->models($request);

        if (!$players) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$players['data']) {
            return $this->responseService->json('Fail!', [], 400, $players['errors']);
        }

        if (!$players['data']->count()) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]);
        }

        $data = new ClubPresidentResource($players['data']->first());
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateClubPresidentRequest $request, $id)
    {
        $club_president = $this->ClubPresidentI->create($request, $id);

        if (!$club_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditClubPresidentRequest $request, $id)
    {
        $club_president = $this->ClubPresidentI->edit($request, $id);

        if (!$club_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $club_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $model = $this->model->where('club_id', $id)->first();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $id = $model->id;
        
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->ClubPresidentI->forceDelete($id);
        } else {
            $deleted = $this->ClubPresidentI->delete($id);
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
