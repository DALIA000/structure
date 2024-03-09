<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\City\CityInterface;
use App\Http\Resources\CitiesListResource;
use App\Services\ResponseService;

class CityController extends Controller
{
    public function __construct(public CityInterface $CityI, public ResponseService $responseService)
    {
    }

    public function cities(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'locale',
            'country',
            'country.locale',
        ]]);

        $models = $this->CityI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CitiesListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['cities' => $data], 200, paginate: 'cities');
    }
}
