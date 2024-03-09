<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Business\BusinessInterface;
use App\Http\Resources\Dashboard\{
    BusinessesListResource,
    BusinessResource,
    BusinessOverviewResource,
};
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Services\ResponseService;

class BusinessController extends Controller
{
    public function __construct(private BusinessInterface $BusinessI, private ResponseService $responseService)
    {
        $this->BusinessI = $BusinessI;
    }

    public function businesses(Request $request)
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

        $businesses = $this->BusinessI->models($request);

        if (!$businesses) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$businesses['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $businesses['errors']]);
        }

        $data = BusinessesListResource::collection($businesses['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function business(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
        ]]);

        $business = $this->BusinessI->findByIdWith($request);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new BusinessResource($business);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $business = $this->BusinessI->accept($request, $id);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$business['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $business['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $business = $this->BusinessI->reject($request, $id);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$business['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $business['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $business = $this->BusinessI->block($request, $id);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$business['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $business['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $business = $this->BusinessI->unblock($request, $id);

        if (!$business) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$business['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $business['errors']]);
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

        $academy = $this->BusinessI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new BusinessOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
