<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\Country\CountryInterface;
use App\Http\Resources\CountriesListResource;
use App\Services\ResponseService;

class CountryController extends Controller
{
    public function __construct(public CountryInterface $CountryI, public ResponseService $responseService)
    {
    }

    public function countries(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'locale',
        ]]);

        $models = $this->CountryI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CountriesListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['countries' => $data], 200, paginate: 'countries');
    }
}
