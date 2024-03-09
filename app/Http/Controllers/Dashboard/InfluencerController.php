<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Influencer\InfluencerInterface;
use App\Http\Resources\Dashboard\{
    InfluencersListResource,
    InfluencerResource,
    InfluencerOverviewResource,
};
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Services\ResponseService;

class InfluencerController extends Controller
{
    public function __construct(private InfluencerInterface $InfluencerI, private ResponseService $responseService)
    {
        $this->InfluencerI = $InfluencerI;
    }

    public function influencers(Request $request)
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

        $influencers = $this->InfluencerI->models($request);

        if (!$influencers) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$influencers['data']) {
            return $this->responseService->json('Fail!', [], 400, $influencers['errors']);
        }

        $data = InfluencersListResource::collection($influencers['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function influencer(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
        ]]);

        $influencer = $this->InfluencerI->findByIdWith($request);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new InfluencerResource($influencer);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $influencer = $this->InfluencerI->accept($request, $id);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$influencer['status']) {
            return $this->responseService->json('Fail!', [], 400, $influencer['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $influencer = $this->InfluencerI->reject($request, $id);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$influencer['status']) {
            return $this->responseService->json('Fail!', [], 400, $influencer['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $influencer = $this->InfluencerI->block($request, $id);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$influencer['status']) {
            return $this->responseService->json('Fail!', [], 400, $influencer['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $influencer = $this->InfluencerI->unblock($request, $id);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$influencer['status']) {
            return $this->responseService->json('Fail!', [], 400, $influencer['errors']);
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

        $influencer = $this->InfluencerI->findByIdWith($request);

        if (!$influencer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new InfluencerOverviewResource($influencer);
        return $this->responseService->json('Success!', $data, 200);
    }
}
