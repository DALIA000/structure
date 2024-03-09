<?php

namespace App\Http\Controllers;

use App\Http\Repositories\User\UserInterface;
use App\Http\Resources\FollowersListResource;
use Illuminate\Http\Request;
use App\Http\Repositories\Follow\FollowInterface;
use App\Services\ResponseService;
use App\Http\Resources\FollowsListResource;
use League\Uri\Contracts\UserInfoInterface;

class FollowController extends Controller
{
    public $FollowI;
    public $loggedinUser;

    public function __construct(FollowInterface $FollowI, public ResponseService $responseService, public UserInterface $UserI)
    {
        $this->FollowI = $FollowI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function followings(Request $request, $username)
    {
        $user = $this->viewableUser($request, $username);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'user_id' => $user->id,
            'self' => $this->loggedinUser?->username == $user->username,
            'is_pending' => 0,
        ]);

        $request->merge(['with' => [
            'user',
            'followable',
        ]]);

        $follows = $this->FollowI->models($request);

        if (!$follows) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$follows['status']) {
            return $this->responseService->json('Fail!', [], 400, $follows['errors']);
        }

        $follows = $follows['data'];

        return $this->responseService->json('Success!', FollowsListResource::collection($follows), 200);
    }

    public function followRequests(Request $request)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'followable_id' => $user->id,
            'is_pending' => 1,
        ]);

        $request->merge(['with' => [
            'user',
            'followable',
        ]]);

        $follows = $this->FollowI->models($request);

        if (!$follows) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$follows['status']) {
            return $this->responseService->json('Fail!', [], 400, $follows['errors']);
        }

        $follows = $follows['data'];

        return $this->responseService->json('Success!', FollowersListResource::collection($follows), 200);
    }

    public function acceptFollowRequests(Request $request, $username)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $follows = $this->FollowI->accept($request, $username);

        if (!$follows) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$follows['status']) {
            return $this->responseService->json('Fail!', [], 400, $follows['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function rejectFollowRequests(Request $request, $username)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $follows = $this->FollowI->reject($request, $username);

        if (!$follows) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$follows['status']) {
            return $this->responseService->json('Fail!', [], 400, $follows['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function followers(Request $request, $username)
    {
        $user = $this->viewableUser($request, $username);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        if (!$request->exists('order') || $request->order === null) {
            $request->merge(['order' => 'desc']);
        }

        if (!$request->exists('sort') || $request->sort === null) {
            $request->merge(['sort' => 'created_at']);
        }

        $request->merge([
            'followable_id' => $user->id,
            'is_pending' => 0,
        ]);

        $request->merge(['with' => [
            'user',
            'followable',
        ]]);

        $follows = $this->FollowI->models($request);

        if (!$follows) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$follows['status']) {
            return $this->responseService->json('Fail!', [], 400, $follows['errors']);
        }

        $follows = $follows['data'];

        return $this->responseService->json('Success!', FollowersListResource::collection($follows), 200);
    }

    private function viewableUser($request, $username) {
        $user = $this->UserI->findByWhere('username', $username, function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                // in loggedin user followings or self

                if ($this->loggedinUser) {
                    $query->where(function ($query) use ($request) {
                        $followings = $this->loggedinUser?->followings?->pluck('followable_id')?->merge([$this->loggedinUser?->id])->unique()->toArray();
                        $query->whereIn('id', $followings);
                    });
                }

                // public accounts
                $query->orWhere(function ($query) use ($request) {
                    $query->whereHas('preferences', function ($query) use ($request) {
                        $query->where('slug', 'account')
                            ->where('value', 1);
                    });
                })

                // pro accounts
                ->orWhere(function ($query) use ($request) {
                    $query->whereDoesntHave('preferences', function ($query) use ($request) {
                        $query->where('slug', 'account');
                    });
                });
            });
        }, function ($query) use ($request) {
            if ($this->loggedinUser) {
                $query->whereHas('blocks', function ($query) use ($request) {
                    $query->where('blockable_id', $this->loggedinUser?->id);
                })->orWhereHas('blocked', function ($query) use ($request) {
                    $query->where('user_id', $this->loggedinUser?->id);
                });
            }
        });

        return $user;
    }
}
