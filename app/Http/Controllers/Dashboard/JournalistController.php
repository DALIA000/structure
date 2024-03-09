<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Journalist\JournalistInterface;
use App\Http\Resources\Dashboard\{
    JournalistsListResource,
    JournalistResource,
    JournalistOverviewResource,
};
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Services\ResponseService;

class JournalistController extends Controller
{
    public function __construct(private JournalistInterface $JournalistI, private ResponseService $responseService)
    {
        $this->JournalistI = $JournalistI;
    }

    public function journalists(Request $request)
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
            'account',
            'status',
            'status.locale',
            'account.city',
            'account.city.locale',
            'account.city.country.locale',
        ]]);

        $journalists = $this->JournalistI->models($request);

        if (!$journalists) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$journalists['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $journalists['errors']]);
        }

        $data = JournalistsListResource::collection($journalists['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function journalist(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
        ]]);

        $journalist = $this->JournalistI->findByIdWith($request);

        if (!$journalist) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new JournalistResource($journalist);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $journalist = $this->JournalistI->accept($request, $id);

        if (!$journalist) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$journalist['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $journalist['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $journalist = $this->JournalistI->reject($request, $id);

        if (!$journalist) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$journalist['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $journalist['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $journalist = $this->JournalistI->block($request, $id);

        if (!$journalist) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$journalist['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $journalist['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $journalist = $this->JournalistI->unblock($request, $id);

        if (!$journalist) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$journalist['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $journalist['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function overview(Request $request, $id)
    {
        $request->merge(['with' => [
            'account' => function ($query) {
                $query->withCount([
                    'videos',
                    'followings',
                    'followers',
                    'tagged_videos',
                ]);
            },
        ], 'withCount' => [
        ]]);

        $academy = $this->JournalistI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new JournalistOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
