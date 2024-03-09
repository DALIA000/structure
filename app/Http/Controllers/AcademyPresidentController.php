<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\AcademyPresident\AcademyPresidentInterface;
use App\Http\Resources\{
    AcademyPresidentsListResource,
    AcademyPresidentResource,
};
use App\Http\Requests\{
    CreateAcademyPresidentRequest,
    EditAcademyPresidentRequest,
    DeleteAcademyPresidentRequest,
};
use App\Services\ResponseService;
use App\Models\AcademyPresident;

class AcademyPresidentController extends Controller
{
    private $loggedinUser;

    public function __construct(private AcademyPresidentInterface $AcademyPresidentI, private ResponseService $responseService, private AcademyPresident $model)
    {
        $this->loggedinUser = app('loggedinUser');
        $this->AcademyPresidentI = $AcademyPresidentI;
    }

    public function president(Request $request)
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
            'academy_id' => $this->loggedinUser?->user?->id
        ]);
        
        $request->merge(['with' => [
        ]]);

        $academy_presidents = $this->AcademyPresidentI->models($request);

        if (!$academy_presidents) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy_presidents['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy_presidents['errors']]);
        }

        $data = AcademyPresidentResource::collection($academy_presidents['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateAcademyPresidentRequest $request)
    {
        $id = $this->loggedinUser?->user?->id;
        $academy_president = $this->AcademyPresidentI->create($request, $id);

        if (!$academy_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy_president['status']) {
            return $this->responseService->json('Fail!', [], 400, $academy_president['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function edit(EditAcademyPresidentRequest $request)
    {
        $id = $this->loggedinUser?->user?->id;
        $academy_president = $this->AcademyPresidentI->edit($request, $id);

        if (!$academy_president) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy_president['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy_president['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request)
    {
        $id = $this->loggedinUser?->user?->id;
        $model = $this->model->where('academy_id', $id)->first();
        if (!$model) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }
        $id = $model->id;
        
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->AcademyPresidentI->forceDelete($id);
        } else {
            $deleted = $this->AcademyPresidentI->delete($id);
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
