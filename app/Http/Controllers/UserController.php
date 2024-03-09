<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\{
    ReportUserRequest,
    ChangeUserTypeRequest
};
use App\Http\Repositories\{
    User\UserInterface,
    Save\SaveInterface,
    Block\BlockInterface,
    Follow\FollowInterface,
    Report\ReportInterface,
};
use App\Http\Repositories\DeleteAccountRequest\DeleteAccountRequestInterface;
use App\Services\ResponseService;
use App\Http\Resources\{
    UserShortResource,
    UserPreferenceResource,
    UsersShortListResource,
    NotificationResource,
    TrainerExperienceLevelsListResource,
    AcademiesListResource
};
use App\Models\{
    Model,
    User,
    Player,
    Trainer
};

class UserController extends Controller
{
    public $UserI;
    public $loggedinUser;

    public function __construct(
        UserInterface $UserI, 
        public ResponseService $responseService, 
        public SaveInterface $SaveI, 
        public BlockInterface $BlockI,
        public FollowInterface $FollowI,
        public ReportInterface $ReportI,
        public User $model,
        public DeleteAccountRequestInterface $DeleteAccountRequestI
    )
    {
        $this->UserI = $UserI;
        $this->model = $model;
        $this->loggedinUser = app('loggedinUser');
    }

    public function users(Request $request)
    {
        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'username' => $request->q,
            'whereUsernameNotIn' => [$this->loggedinUser?->username]
        ]);

        $request->merge([
            'with' => [],
            'withCount' => [
                'followers' => function ($query) {
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

        return $this->responseService->json('Success!', $data, 200);

    }

    public function user(Request $request, $username)
    {
        $user = $this->loggedinUser;

        $request->merge([ 'withCount' => [
            'follows',
            'followers' => function ($query) {
                return $query->Where('is_pending', 0);
            },
            'videos',
        ]]);

        $viewable = $this->UserI->findByWith('username', $username, $request, [], function ($query) use ($request) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$viewable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]);
        }

        $preferences = $this->UserI->preferences($request, $viewable->id, 'section');
        if (!$preferences) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$preferences['status']) {
            return $this->responseService->json('Fail!', [], 400, ['error' => $preferences['errors']]);
        }

        $preferences = $preferences['data'];

        // get extra data based on user type
        switch ($viewable->user_type?->user_type) {
            case Player::class:
                $academy = $viewable->user?->academy_player?->academy;
                $info = [
                    'player_position' => $viewable->user?->player_position?->locale?->name,
                    'player_footness' => $viewable->user?->player_footness?->locale?->name,
                    'academy' => $academy ? new AcademiesListResource($academy) : $viewable?->user?->other_academy,
                ];
                break;
            case Trainer::class:
                $info = [
                    'trainer_experience_level' => new TrainerExperienceLevelsListResource($viewable->user?->trainer_experience_level),
                    'achievements' => $viewable->user?->achievements,
                ];
                break;
            default:
                $info = [];
                break;
        }

        return $this->responseService->json('Success!', [
            'user' => new UserShortResource($viewable),
            'is_saved' => (Boolean) $viewable->has_saved?->count(),
            'has_followed' => (Boolean) $viewable->has_followed?->count(),
            'has_sent_follow_request' => (Boolean) $viewable->has_sent_follow_request?->count(),
            'preferences' => UserPreferenceResource::collection($preferences),
            'info' => [
                ...$info,
                'birthday' => $viewable->birthday,
                'country' => $viewable->city?->country?->locale?->name,
                'city' => $viewable->city?->locale?->name,
            ]
        ], 200);
    }

    public function notifications(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $notifications = $this->UserI->notifications($request);

        if (!$notifications) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$notifications['status']) {
            return $this->responseService->json('Fail!', [], 400, $notifications['errors']);
        }

        $data = NotificationResource::collection($notifications['data']);
        return $this->responseService->json('Success!', $data, 200);
    }

    public function readNotification(Request $request, $id)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $notifications = $this->UserI->readNotification($id);

        if (!$notifications) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$notifications['status']) {
            return $this->responseService->json('Fail!', [], 400, $notifications['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function changeUserTypeRequest(ChangeUserTypeRequest $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $changed = $this->UserI->changeUserTypeRequest($request, $user->id);

        if (!$changed) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$changed['status']) {
            return $this->responseService->json('Fail!', [], 400, $changed['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function cancelChangeUserTypeRequest(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $canceled = $this->UserI->cancelChangeUserTypeRequest($request, $user->id);

        if (!$canceled) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$canceled['status']) {
            return $this->responseService->json('Fail!', [], 400, $canceled['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function save(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $savable = $this->UserI->findByWhere('username', $username, [], function ($query) use ($request) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$savable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

        $request->merge([
            'savable_id' => $savable->id,
            'savable_type' => get_class($savable),
        ]);

        $save = $this->SaveI->create($request, $user->id);

        if (!$save) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$save['status']) {
            return $this->responseService->json('Fail!', [], 400, $save['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unsave(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $savable = $this->UserI->findByUsername($username);
        if (!$savable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

        $request->merge([
            'savable_id' => $savable->id,
            'savable_type' => get_class($savable),
        ]);

        $save = $this->SaveI->unsave($request, $user->id);

        if (!$save) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$save['status']) {
            return $this->responseService->json('Fail!', [], 400, $save['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function block(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $blockable = $this->UserI->findByUsername($username);
        if (!$blockable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]);
        }

        $request->merge([
            'blockable_id' => $blockable->id,
            'blockable_type' => get_class($blockable),
        ]);

        $block = $this->BlockI->create($request, $user->id);

        if (!$block) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$block['status']) {
            return $this->responseService->json('Fail!', [], 400, $block['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unblock(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $blockable = $this->UserI->findByUsername($username);
        if (!$blockable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

        $request->merge([
            'blockable_id' => $blockable->id,
            'blockable_type' => get_class($blockable),
        ]);

        $block = $this->BlockI->unblock($request, $user->id);

        if (!$block) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$block['status']) {
            return $this->responseService->json('Fail!', [], 400, $block['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function follow(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $followable = $this->UserI->findByWhere('username', $username, [], function ($query) use ($request) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$followable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]);
        }

        if ($followable->id === $user->id) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->UserI->model)])]);
        }

        $request->merge([
            'followable_id' => $followable->id,
            'followable_type' => get_class($followable),
        ]);

        $is_pending = $followable->account_is_public ? 0 : 1;
        $follow = $this->FollowI->create($request, $user->id, $is_pending);

        if (!$follow) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$follow['status']) {
            return $this->responseService->json('Fail!', [], 400, $follow['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function unfollow(Request $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $followable = $this->UserI->findByUsername($username);
        if (!$followable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
        }

        $request->merge([
            'followable_id' => $followable->id,
            'followable_type' => get_class($followable),
        ]);

        $follow = $this->FollowI->unfollow($request, $user->id);

        if (!$follow) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$follow['status']) {
            return $this->responseService->json('Fail!', [], 400, $follow['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function report(ReportUserRequest $request, $username)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $reportable = $this->UserI->findByWhere('username', $username, [], function ($query) use ($request) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            });
        if (!$reportable) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('crud.notfound', ['model' => trans_class_basename($this->model)])]);
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

    public function insights(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }
        $data = $this->UserI->insights($request);
        if (!$data) {
            return $this->responseService->json('no data found!', [], 400);
        }
        return $this->responseService->json('Success!', $data, 200);
    }

    public function deleteAccountRequest(Request $request)
    {
        $user = $this->loggedinUser;
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $request->merge([
            'user_id' => $this->loggedinUser?->user?->id,
            'full_name' => $this->loggedinUser?->full_name,
            'user_type' => $this->loggedinUser?->user_type_class,
            'followings_count' => $this->loggedinUser?->followings()->count(),
            'followers_count' => $this->loggedinUser?->followers()->count(),
        ]);

        $data = $this->DeleteAccountRequestI->create($request);
        if (!$data) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$data['status']) {
            return $this->responseService->json('Fail!', [], 400, $data['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }
}
