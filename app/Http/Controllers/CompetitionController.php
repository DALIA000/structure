<?php

namespace App\Http\Controllers;

use App\Http\Repositories\Like\LikeInterface;
use App\Http\Repositories\Report\ReportInterface;
use App\Http\Repositories\Share\ShareInterface;
use App\Http\Repositories\Status\StatusInterface;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\CreateUserCompetitionOptionRequest;
use App\Http\Resources\CommentListResource;
use App\Http\Resources\CompetitionResource;
use App\Http\Resources\UserChatListResource;
use App\Http\Resources\UserCompetitionOptionResource;
use App\Models\Model;
use App\Http\Repositories\View\ViewInterface;
use App\Models\UserCompetitionOption;
use Illuminate\Http\Request;
use App\Http\Repositories\{
    Competition\CompetitionInterface,
    User\UserInterface,
    Comment\CommentInterface,
    CompetitionSubscribtion\CompetitionSubscribtionInterface
};
use App\Services\ResponseService;
use App\Http\Requests\CreateCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use App\Http\Resources\CompetitionsListResource;

class CompetitionController extends Controller
{
    public $loggedinUser;

    public function __construct(public CompetitionInterface $CompetitionI, public UserInterface $UserI, public CompetitionSubscribtionInterface $CompetitionSubscribtionI, public ResponseService $responseService)    {
        $this->loggedinUser = app('loggedinUser');
    }

    public function competitions(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        // $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'subscriber_id' => $this->loggedinUser?->id
            // 'status_id' => $status_id,
        ]);
            
        $request->merge(['with' => [
            // 'user',
        ], 'withCount' => [
            // 'likes',
            // 'has_liked',
            // 'comments',
            // 'views',
            // 'shares'
        ]]);

        $models = $this->CompetitionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $data = CompetitionsListResource::collection($models['data']);

        return $this->responseService->json('Success!', ['competitions' => $data], 200, paginate: 'competitions');
    }

    public function competition(Request $request, $id)
    {
        // $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'id' => $id,
            'subscriber_id' => $this->loggedinUser?->id
            // 'status_id' => $status_id,
        ]);
            
        $request->merge(['with' => [
            // 'user',
            'options',
        ], 'withCount' => [
            // 'likes',
            // 'has_liked',
            // 'comments',
            // 'views',
            // 'shares'
        ]]);

        $models = $this->CompetitionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $model = $models['data']->first();
        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->CompetitionI->model)])]]);
        }

        $data = new CompetitionResource($model);

        return $this->responseService->json('Success!', ['competition' => $data], 200);
    }
    
    public function create(CreateCompetitionRequest $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        // $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'user_id' => $user->id,
            // 'status_id' => $status_id,
        ]);

        $video = $this->CompetitionI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        $data = $video['data'];
        return $this->responseService->json('Success!', $data->only('id'), 200);
    }

    public function update(UpdateCompetitionRequest $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->model)])]]];
        }

        // $status_id = $this->StatusI->findBySlug('active')?->id;

        $request->merge([
            'user_id' => $user->id,
            // 'status_id' => $status_id,
        ]);

        $video = $this->CompetitionI->update($request, $id);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        $data = $video['data'];
        return $this->responseService->json('Success!', $data->only('id'), 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $competition = $this->CompetitionI->delete($request);

        if (!$competition) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$competition['status']) {
            return $this->responseService->json('Fail!', [], 400, $competition['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function view(Request $request, $id)
    {
        $request->merge([
            'ip' => $request->ip(),
            'model_id' => $id,
            'model_type' => get_class($this->CompetitionI->model),
        ]);

        $video = $this->ViewI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function share(Request $request, $id)
    {
        $request->merge([
            'ip' => $request->ip(),
            'model_id' => $id,
            'model_type' => get_class($this->CompetitionI->model),
        ]);

        $video = $this->ShareI->create($request);

        if (!$video) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$video['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $video['errors']]);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function report(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $reportable = $this->CompetitionI->findByWhere('id', $id, ['status_id' => 1]);
        if (!$reportable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->CompetitionI->model)])]);
        }

        $request->merge([
            'reportable_id' => $reportable->id,
            'reportable_type' => get_class($reportable),
        ]);

        $report = $this->ReportI->create($request, $user->id);

        if (!$report) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$report['status']) {
            return $this->responseService->json('Fail!', [], 400, $report['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function subscribe(Request $request, $id)
    {
        $subscribe = $this->CompetitionI->subscribe($request, $id);

        if (!$subscribe) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$subscribe['status']) {
            return $this->responseService->json('Fail!', [], 400, $subscribe['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unsubscribe(Request $request, $id)
    {
        $unsubscribe = $this->CompetitionI->unsubscribe($request, $id);

        if (!$unsubscribe) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$unsubscribe['status']) {
            return $this->responseService->json('Fail!', [], 400, $unsubscribe['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function subscribers(Request $request, $id)
    {
        // $status_id = $this->StatusI->findBySlug('active')?->id;
        $request->merge([
            'id' => $id,
            'subscriber_id' => $this->loggedinUser?->id
            // 'status_id' => $status_id,
        ]);
            
        $request->merge(['with' => [
            'subscribers'
        ], 'withCount' => [
        ]]);

        $models = $this->CompetitionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $model = $models['data']->first();

        if (!$model) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->CompetitionI->model)])]]);
        }

        $data = UserChatListResource::collection($model->subscribers);

        return $this->responseService->json('Success!', ['competitions' => $data], 200, paginate: 'competitions');
    }

    public function participations(Request $request, $id)
    {
        // $status_id = $this->StatusI->findBySlug('active')?->id;
        $request->merge([
            'competition_id' => $id,
            'subscriber_id' => $this->loggedinUser?->id
            // 'status_id' => $status_id,
        ]);

        $request->merge([
            'with' => [
                'user',
                'user.user_competition_options' => function ($query) use ($id) {
                    $query->whereHas('option', function ($query) use ($id) {
                        $query->where('competition_id', $id);
                    });
                }
            ]
        ]);

        $models = $this->CompetitionSubscribtionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $participations = $models['data']->pluck('user');

        $data = UserCompetitionOptionResource::collection($participations);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function winner(Request $request, $id)
    {
        $user = $this->UserI->findByUsername($request->user);
        if (!$user) {
            return ['status' => false, 'errors' => ['error' => [trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]]];
        }
        // $status_id = $this->StatusI->findBySlug('active')?->id;
        $request->merge([
            'competition_id' => $id,
            'subscriber_id' => $this->loggedinUser?->id,
            'has_right_answer' => 1,
            'user_id' => $user->id
        ]);

        $request->merge([
            'with' => ['user']
        ]);

        $models = $this->CompetitionSubscribtionI->models($request);
        if (!$models) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$models['status']) {
            return $this->responseService->json('Fail!', [], 400, $models['errors']);
        }

        $models = $models['data'];
        if ($models->where('status', 1)->count() >= 3) {
            return $this->responseService->json('Fail!', [], 400, ['errors' => ['messages.winnersLimit']]);
        }

        $participation = $models->first();
        if (!$participation) {
            return $this->responseService->json('Fail!', [], 400, ['errors' => ['crud.notfound']]);
        }

        $participation->update([
            'status' => 1,
        ]);

        return $this->responseService->json('Success!', [], 200);
    }

    public function participate(CreateUserCompetitionOptionRequest $request, $id)
    {
        $participation = $this->CompetitionI->participate($request, $id);

        if (!$participation) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$participation['status']) {
            return $this->responseService->json('Fail!', [], 400, $participation['errors']);
        }

        return $this->responseService->json('Success!', [
            'right_answer' => $participation['data']?->id
        ], 200);
    }
}
