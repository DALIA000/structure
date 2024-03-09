<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\City\CityInterface;
use App\Http\Requests\Dashboard\{
    CreateCityRequest,
    EditCityRequest,
};
use App\Http\Resources\Dashboard\{
    CitiesListResource,
    CityResource,
    CityShortResource,
};
use App\Services\ResponseService;

class CityController extends Controller
{
    public function __construct(private CityInterface $CityI, private ResponseService $responseService)
    {
        $this->CityI = $CityI;
    }

    public function cities(Request $request)
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
            'country',
            'country.locale',
            'country.currency',
            'country.currency.locale',
        ]]);

        $cities = $this->CityI->models($request);

        if (!$cities) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$cities['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $cities['errors']]);
        }

        $data = CitiesListResource::collection($cities['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function city(Request $request)
    {
        $request->merge(['with' => [
            'locales',
        ]]);

        $city = $this->CityI->findByIdWith($request);
        if (!$city) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->CityI->model)])]]);
        }
        $data = new CityResource($city);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateCityRequest $request)
    {
        $city = $this->CityI->create($request);
        if (!$city) {
            return $this->responseService->json('Fail!', [], 400, ['city' => [trans('messages.error')]]);
        }

        if (!$city['status']) {
            return $this->responseService->json('Fail!', [], 400, ['city' => [trans('crud.create', ['model' => trans_class_basename($this->CityI->model)])]]);
        }

        $data = new CityShortResource($city['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function edit(EditCityRequest $request, $id)
    {
        $city = $this->CityI->edit($request, $id);
        if (!$city) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        if (!$city['status']) {
            return $this->responseService->json('Fail!', [], 400, $city['errors']);
        }
        $data = new CityShortResource($city['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function delete(Request $request, $id)
    {
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->CityI->forceDelete($id);
        } else {
            $deleted = $this->CityI->delete($id);
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
