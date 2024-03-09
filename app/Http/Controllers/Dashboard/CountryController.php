<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Repositories\Country\CountryInterface;
use App\Http\Requests\Dashboard\CreateCountryRequest;
use App\Http\Requests\Dashboard\EditCountryRequest;
use App\Http\Resources\Dashboard\CountryResource;
use App\Http\Resources\Dashboard\CountriesListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CountryController extends Controller
{

    public function __construct(private CountryInterface $CountryI, private ResponseService $responseService)
    {
    }

    public function countries(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        $request->merge(['with' => [
            'locale',
        ], 'withCount' => [
            'cities'
        ]]);

        $countries = $this->CountryI->models($request);

        if (!$countries) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$countries['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $countries['errors']]);
        }

        $data = CountriesListResource::collection($countries['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function country(Request $request)
    {
        $request->merge(['with' => [
            'locales',
        ]]);

        $country = $this->CountryI->findByIdWith($request);
        if (!$country) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->CountryI->model)])]]);
        }
        $data = new CountryResource($country);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(CreateCountryRequest $request)
    {
        $country = $this->CountryI->create($request);
        if (!$country) {
            return $this->responseService->json('Fail!', [], 400, ['country' => [trans('messages.error')]]);
        }

        if (!$country['status']) {
            return $this->responseService->json('Fail!', [], 400, ['country' => [trans('crud.create', ['model' => trans_class_basename($this->CountryI->model)])]]);
        }

        $data = new CountryResource($country['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function edit(EditCountryRequest $request, $id)
    {
        $country = $this->CountryI->edit($request, $id);
        if (!$country) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        if (!$country['status']) {
            return $this->responseService->json('Fail!', [], 400, $country['errors']);
        }
        return $this->responseService->json('Success!', [], 200);
    }

    public function delete(Request $request, $id)
    {
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->CountryI->forceDelete($id);
        } else {
            $deleted = $this->CountryI->delete($id);
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
