<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\Trainer\TrainerInterface;
use App\Http\Resources\Dashboard\{
    TrainersListResource,
    TrainerResource,
    TrainerOverviewResource,
};
use App\Http\Requests\Dashboard\RejectUserRequest;
use App\Services\ResponseService;

class TrainerController extends Controller
{
    public function __construct(private TrainerInterface $TrainerI, private ResponseService $responseService)
    {
        $this->TrainerI = $TrainerI;
    }

    public function trainers(Request $request)
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
            'trainer_experience_level',
            'trainer_experience_level.locale',
        ]]);

        $trainers = $this->TrainerI->models($request);

        if (!$trainers) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$trainers['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $trainers['errors']]);
        }

        $data = TrainersListResource::collection($trainers['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function trainer(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
            'trainer_experience_level',
            'trainer_experience_level.locale',
        ], 'withCount' => [
        ]]);

        $trainer = $this->TrainerI->findByIdWith($request);

        if (!$trainer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new TrainerResource($trainer);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function accept(Request $request, $id)
    {
        $trainer = $this->TrainerI->accept($request, $id);

        if (!$trainer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$trainer['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $trainer['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function reject(RejectUserRequest $request, $id)
    {
        $trainer = $this->TrainerI->reject($request, $id);

        if (!$trainer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$trainer['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $trainer['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $id)
    {
        $trainer = $this->TrainerI->block($request, $id);

        if (!$trainer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$trainer['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $trainer['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $trainer = $this->TrainerI->unblock($request, $id);

        if (!$trainer) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$trainer['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $trainer['errors']]);
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
                    'courses'
                ]);
            },
        ], 'withCount' => [
        ]]);

        $academy = $this->TrainerI->findByIdWith($request);

        if (!$academy) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new TrainerOverviewResource($academy);
        return $this->responseService->json('Success!', $data, 200);
    }
}
