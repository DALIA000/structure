<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Academy\AcademyInterface;
use App\Http\Resources\Dashboard\{
    AcademiesListResource,
    AcademyResource,
    AcademyOverviewResource,
};
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Services\ResponseService;

class AcademyController extends Controller
{
    public function __construct(private AcademyInterface $AcademyI, private ResponseService $responseService)
    {
        $this->AcademyI = $AcademyI;
    }

    public function academies(Request $request)
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
            'academy_level',
            'academy_level.locale',
        ]]);

        $academies = $this->AcademyI->models($request);

        if (!$academies) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academies['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academies['errors']]);
        }

        $data = AcademiesListResource::collection($academies['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function academy(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
            'academy_level',
            'academy_level.locale',
        ], 'withCount' => [
            'players',
        ]]);

        $academy = $this->AcademyI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new AcademyResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $academy = $this->AcademyI->accept($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $academy = $this->AcademyI->reject($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $academy = $this->AcademyI->block($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $academy = $this->AcademyI->unblock($request, $id);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$academy['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $academy['errors']]);
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
                    'followers'
                ]);
            },
        ], 'withCount' => [
            'players',
        ]]);

        $academy = $this->AcademyI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new AcademyOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
