<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AcademyPresident\AcademyPresidentInterface;
use App\Http\Resources\Dashboard\AcademyPresidentResource;
use App\Models\AcademyPresident;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class AcademyPresidentController extends Controller
{
    private $loggedinUser;

    public function __construct(private AcademyPresidentInterface $AcademyPresidentI, private ResponseService $responseService, private AcademyPresident $model)
    {
        $this->AcademyPresidentI = $AcademyPresidentI;
    }

    public function president(Request $request, $id)
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
            'academy_id' => $request->id,
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

}
