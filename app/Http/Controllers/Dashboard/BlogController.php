<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Repositories\Blog\BlogInterface;
use App\Http\Requests\Dashboard\StoreBlogRequest;
use App\Http\Requests\Dashboard\UpdateBlogRequest;
use App\Http\Resources\BlogsListResource;
use App\Http\Resources\AcademyResource;
use App\Http\Resources\Dashboard\BlogResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogController extends Controller
{
    private $BlogI;

    public function __construct(BlogInterface $BlogI, public ResponseService $responseService)
    {
        $this->BlogI = $BlogI;
    }

    public function blogs(Request $request)
    {
        if (!$request->exists('order') || $request->order == null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort == null) {
            $request->merge(['sort' => 'updated_at']);
        }

        $request->merge(['with' => [
            'media'
        ], 'withCount' => [
            'media',
            'likes',
            'comments',
            'views',
        ]]);

        $blogs = $this->BlogI->models($request);

        if (!$blogs) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$blogs['data']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $blogs['errors']]);
        }

        $data = BlogsListResource::collection($blogs['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function blog(Request $request, $id)
    {
        if (!$request->exists('country_slug') || $request->country_slug == null) {
            $request->merge(['country_slug' => app('country')]);
        }

        $request->merge(['with' => [
            'media'
        ], 'withCount' => [
            'media',
            'likes',
            'comments',
            'views',
        ]]);

        $blog = $this->BlogI->findByIdWith($request);

        if (!$blog) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('ceud.notfound')]]);
        }

        $data = new BlogResource($blog);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function create(StoreBlogRequest $request)
    {
        $blog = $this->BlogI->create($request);

        if (!$blog) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$blog['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $blog['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function update(UpdateBlogRequest $request, $id)
    {
        $club = $this->BlogI->update($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function activate(Request $request, $id)
    {
        $club = $this->BlogI->activate($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function archive(Request $request, $id)
    {
        $club = $this->BlogI->archive($request, $id);

        if (!$club) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$club['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $club['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function destroy(Request $request, $id)
    {
        $deleted = null;
        $request->merge(['force' => 1]);

        if ($request->exists('force') && $request->force == true) {
            $deleted = $this->BlogI->forceDelete($id);
        } else {
            $deleted = $this->BlogI->delete($id);
        }
        if (!$deleted) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }
        if (!$deleted['status']) {
            return $this->responseService->json('Fail!', [], 400, $deleted['errors']);
        }
        return $this->responseService->json('Success!', [], 200);
    }
}
