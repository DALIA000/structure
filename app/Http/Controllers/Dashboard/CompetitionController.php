<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Competition\CompetitionInterface;
use App\Http\Resources\Dashboard\CompetitionResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public $CompetitionI;
    public $loggedinUser;

    public function __construct(CompetitionInterface $CompetitionI, public ResponseService $responseService){
        $this->CompetitionI = $CompetitionI;
        $this->loggedinUser = app('loggedinUser');
    }
    public function Competitions(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge(['with' => [
            'club',
        ]]);

        $models = $this->CompetitionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CompetitionResource::collection($models['data']);

        return $this->responseService->json('Success!', $data, 200);
    }
}
