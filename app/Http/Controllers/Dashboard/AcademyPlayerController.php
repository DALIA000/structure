<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Repositories\AcademyPlayer\AcademyPlayerInterface;
use App\Http\Resources\AcademyPlayerResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class AcademyPlayerController extends Controller
{
    public function __construct(private AcademyPlayerInterface $AcademyPlayerI, private ResponseService $responseService)
    {
        $this->AcademyPlayerI = $AcademyPlayerI;
    }
    public function players(Request $request)
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
            'club',
            'player_position',
            'player_position.locale',
        ]]);

        $players = $this->AcademyPlayerI->models($request);

        if (!$players) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$players['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $players['errors']]);
        }

        $data = AcademyPlayerResource::collection($players['data']);
        return $this->responseService->json('Success!', $data, 200);
    }
}
