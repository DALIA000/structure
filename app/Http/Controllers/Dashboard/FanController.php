<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Fan\FanInterface;
use App\Http\Resources\Dashboard\{
    FansListResource,
    FanResource,
    FanOverviewResource
};
use App\Services\ResponseService;

class FanController extends Controller
{
    public function __construct(private FanInterface $FanI, private ResponseService $responseService)
    {
        $this->FanI = $FanI;
    }

    public function fans(Request $request)
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
        ]]);

        $fans = $this->FanI->models($request);

        if (!$fans) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$fans['data']) {
            return $this->responseService->json('Fail!', [], 400, $fans['errors']);
        }

        $data = FansListResource::collection($fans['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function fan(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
        ]]);

        $fan = $this->FanI->findByIdWith($request);

        if (!$fan) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new FanResource($fan);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function block(Request $request, $id)
    {
        $fan = $this->FanI->block($request, $id);

        if (!$fan) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$fan['status']) {
            return $this->responseService->json('Fail!', [], 400, $fan['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $fan = $this->FanI->unblock($request, $id);

        if (!$fan) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$fan['status']) {
            return $this->responseService->json('Fail!', [], 400, $fan['errors']);
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
                    'tagged_videos'
                ]);
            },
        ], 'withCount' => [
        ]]);

        $academy = $this->FanI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new FanOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
