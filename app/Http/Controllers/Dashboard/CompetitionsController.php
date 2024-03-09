<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Competition\CompetitionInterface;
use App\Http\Resources\Dashboard\CompetitionsResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class CompetitionsController extends Controller
{
    public function __construct(private CompetitionInterface $CompetieionI, private ResponseService $responseService){
        $this->CompetieionI = $CompetieionI;
    }

    public function competitions (Request $request)
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

        $clubs = $this->CompetieionI->models($request);

        if (!$clubs) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$clubs['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $clubs['errors']]);
        }

        $data = CompetitionsResource::collection($clubs['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

}
