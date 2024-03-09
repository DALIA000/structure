<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Repositories\SpamSection\SpamSectionInterface;
use App\Http\Resources\Dashboard\{
    SpamSectionsListResource,
    SpamSectionResource,
};
use App\Services\ResponseService;

class SpamSectionController extends Controller
{
    public function __construct(private SpamSectionInterface $SpamSectionI, private ResponseService $responseService)
    {
        $this->SpamSectionI = $SpamSectionI;
    }

    public function spam_sections(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'asc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'created_at']);
        }

        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            // 'spams',
            // 'spams.locale',
        ]]);

        $spam_sections = $this->SpamSectionI->models($request);

        if (!$spam_sections) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spam_sections['data']) {
            return $this->responseService->json('Fail!', [], 400, $spam_sections['errors']);
        }

        $data = SpamSectionsListResource::collection($spam_sections['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function spam_section(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'status',
            'status.locale',
        ], 'withCount' => [
        ]]);

        $spam_section = $this->SpamSectionI->findByIdWith($request);

        if (!$spam_section) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new SpamSectionResource($spam_section);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function block(Request $request, $id)
    {
        $spam_section = $this->SpamSectionI->block($request, $id);

        if (!$spam_section) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spam_section['status']) {
            return $this->responseService->json('Fail!', [], 400, $spam_section['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $id)
    {
        $spam_section = $this->SpamSectionI->unblock($request, $id);

        if (!$spam_section) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$spam_section['status']) {
            return $this->responseService->json('Fail!', [], 400, $spam_section['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
