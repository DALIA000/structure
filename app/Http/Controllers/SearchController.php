<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\{
    User\UserInterface,
    Video\VideoInterface,
    Blog\BlogInterface,
    Status\StatusInterface,
    Hashtag\HashtagInterface,
};
use App\Services\ResponseService;
use App\Http\Resources\{
    UsersShortListResource,
    VideosListResource,
    BlogsListResource,
    HashtagsListResource,
};
use App\Models\{
    Model,
    Video,
    User,
    Blog,
};

class SearchController extends Controller
{
    public $loggedinUser;

    public function __construct(public UserInterface $UserI, public VideoInterface $VideoI, public BlogInterface $BlogI, public HashtagInterface $HashtagI, public StatusInterface $StatusI, public ResponseService $responseService)
    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function search (Request $request)
    {
        $request->merge([
            'search' => $request->q,
        ]);

        switch ($request->type) {
            case 'accounts':
                $data = $this->users($request);
                $model_type = Model::where('type', User::class)->first();
                break;

            case 'videos':
                $data = $this->videos($request);
                $model_type = Model::where('type', Video::class)->first();
                break;

            case 'blogs':
                $data = $this->blogs($request);
                $model_type = Model::where('type', Blog::class)->first();
                break;
            
            default:
                $data = $this->users($request);
                $model_type = Model::where('type', User::class)->first();
                break;
        }

        $hashtags = $this->hashtags($request, $model_type->id);

        return $this->responseService->json('Success!', [
            'hashtags' => $hashtags,
            'models' => $data,
        ], 200, [], 'models');
    }

    public function users (Request $request)
    {
        $request->merge(['order' => 'desc']);
        $request->merge(['sort' => 'followers_count']);

        $request->merge([
            'search' => $request->q,
            'whereIdNotIn' => $this->loggedinUser ? [$this->loggedinUser?->id] : []
        ]);

        $request->merge([
            'with' => [],
            'withCount' => [
                'followers' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'follows' => function ($query) {
                    return $query->where('is_pending', 0);
                },
                'videos' => function ($query) {
                    return $query->where('status_id', 1);
                }
            ]
        ]);
        
        $models = $this->UserI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = UsersShortListResource::collection($models['data']);

        return $data;
    }

    public function videos(Request $request)
    {
        $request->merge(['order' => 'desc']);
        $request->merge(['sort' => 'likes_count']);

        $status_id = $this->StatusI->findBySlug('active')?->id;
        $request->merge([
            'status_id' => $status_id,
        ]);

        $request->merge(['with' => [
            'user',
        ], 'withCount' => [
            'likes',
            'has_liked',
            'comments',
            'views',
            'shares'
        ]]);

        $models = $this->VideoI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = VideosListResource::collection($models['data']);

        return $data;
    }

    public function blogs(Request $request)
    {
        $request->merge(['order' => 'desc']);
        $request->merge(['sort' => 'created_at']);

        $request->merge(['with' => [
            'locales',
            'media',
            'tags',
        ], 'withCount' => [
            'likes',
            'comments',
            'views',
        ]]);

        $request->merge([
            'status_id' => 1,
        ]);

        $models = $this->BlogI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = BlogsListResource::collection($models['data']);

        return $data;
    }

    public function hashtags(Request $request, $model_type_id)
    {
        $request->merge(['order' => 'desc']);
        $request->merge(['sort' => 'count']);

        $request->merge(['with' => [
        ], 'withCount' => [
        ]]);

        $request->merge([
            'group_by' => 'hashtag',
            'model_id' => $model_type_id,
        ]);

        $models = $this->HashtagI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = HashtagsListResource::collection($models['data']);

        return $data;
    }
}
