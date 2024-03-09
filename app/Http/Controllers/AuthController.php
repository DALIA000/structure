<?php

namespace App\Http\Controllers;

use App\Services\Social\{
    Facebook,
    Google,
    Apple,
    Twitter,
};
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Repositories\User\UserInterface;
use App\Http\Requests\{
    CreateUserRequest,
    EditUserRequest,
    UserLoginRequest,
};
use App\Http\Requests\{
    VerificationCodeRequest,
    VerifyRequest,
    ForgetPasswordGetCodeRequest,
    CheckPinRequest,
    ForgetPasswordResetPasswordRequest,
    SocialCheckRequest,
    SocialConnectRequest,
    ChangePasswordRequest,
    EditPreferenceRequest,
};
use App\Http\Resources\{
    UserResource,
    UserMinifiedResource,
    UserPreferenceResource,
};
use App\Services\ResponseService;
use Musonza\Chat\Chat;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public $UserI;
    public $loggedinUser;

    public function __construct(UserInterface $UserI, public Chat $chat, public ResponseService $responseService)
    {
        $this->UserI = $UserI;
        $this->loggedinUser = app('loggedinUser');
    }

    public function signup(CreateUserRequest $request)
    {
        $user = $this->UserI->create($request);
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['user' => [trans('crud.create', ['model' => trans_class_basename($this->UserI->model)])]]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        $token = $user['token'];
        $user = $user['data'];
        $data = new UserMinifiedResource($user);

        return $this->responseService->json('Success!', ['token' => $token, 'user' => $data], 200);
    }

    public function login(UserLoginRequest $request)
    {
        $user = $this->UserI->login($request);
        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        $token = $user['data']['token'];
        $user = $user['data']['user'];

        $unread_messages_count = $this->chat->messages()->setParticipant($user)->unreadCount();

        return $this->responseService->json('Success!', ['token' => $token, 'user' => new UserMinifiedResource($user), 'unread_messages_count' => $unread_messages_count], 200);
    }

    public function logout(Request $request)
    {
        $user = $this->UserI->logout($request);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('messages.error')]]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        return $this->responseService->json('Success!', ['message' => [trans('auth.logout.successed')]], 200);
    }

    public function profile(Request $request)
    {
        $request->merge(['with' => [
            'active_subscribtion'
        ], 'withCount' => [
            'follows',
            'followers' => function ($query) {
                return $query->Where('is_pending', 0);
            },
            'notifications' => function ($query) {
                return $query->Where('read_at', null);
            },
            'videos',
        ]]);

        $user = $this->UserI->profile($request);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        $token = $user['data']['token'];
        $user = $user['data']['user'];

        return $this->responseService->json('Success!', ['token' => $token, 'user' => new UserResource($user)], 200);
    }

    public function preferences(Request $request)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $preferences = $this->UserI->preferences($request, $user->id);

        if (!$preferences) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$preferences['status']) {
            return $this->responseService->json('Fail!', [], 400, $preferences['errors']);
        }

        $preferences = $preferences['data'];

        return $this->responseService->json('Success!', ['preferences' => UserPreferenceResource::collection($preferences)], 200);
    }

    public function editPreference(EditPreferenceRequest $request)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('auth.forbidden')]);
        }

        $preference = $this->UserI->editPreference($request, $user->id);

        if (!$preference) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$preference['status']) {
            return $this->responseService->json('Fail!', [], 400, $preference['errors']);
        }

        $preference = $preference['data'];

        return $this->responseService->json('Success!', new UserPreferenceResource($preference), 200);
    }

    public function edit(EditUserRequest $request)
    {
        $user = $this->loggedinUser;
        $user = $this->UserI->edit($request, $user?->id);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        $user = $user['data'];

        return $this->responseService->json('Success!', ['user' => new UserResource($user)], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if (!$user = $this->loggedinUser) {
            return $this->responseService->json('Fail!', [], 400, ['error' => [trans('auth.forbidden')]]);
        }

        $user = $this->UserI->changePassword($request, $user->id);

        if (!$user) {
            return $this->responseService->json('Fail!', [], 400, [trans('messages.error')]);
        }

        if (!$user['status']) {
            return $this->responseService->json('Fail!', [], 400, $user['errors']);
        }

        $user = $user['data'];

        return $this->responseService->json('Success!', [], 200);
    }

    public function forgetPasswordGetCode(ForgetPasswordGetCodeRequest $request)
    {
        $code = $this->UserI->forgetPasswordGetCode($request);

        if (!$code) {
            return $this->responseService->json('Fail!', [], 400, ['error']);
        }

        if (!$code['status']) {
            return $this->responseService->json('Fail!', [], 400, $code['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function forgetPasswordCheckCode(CheckPinRequest $request)
    {
        $code = $this->UserI->forgetPasswordCheckCode($request);

        if (!$code) {
            return $this->responseService->json('Fail!', [], 400, ['errors' => trans('messages.error')]);
        }

        if (!$code['status']) {
            return $this->responseService->json('Fail!', [], 400, $code['errors']);
        }

        $data = $code['data'];

        return $this->responseService->json('Success!', ['code' => $data], 200);
    }

    public function forgetPasswordResetPassword(ForgetPasswordResetPasswordRequest $request)
    {
        $code = $this->UserI->forgetPasswordResetPassword($request);

        if (!$code) {
            return $this->responseService->json('Fail!', [], 400, ['errors' => trans('messages.error')]);
        }

        if (!$code['status']) {
            return $this->responseService->json('Fail!', [], 400, $code['errors']);
        }

        $data = $code['data'];
        return $this->responseService->json('Success!', $data, 200);
    }

    public function verificationCode(VerificationCodeRequest $request)
    {
        $code = $this->UserI->verificationCode($request);

        if (!$code) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$code['status']) {
            return $this->responseService->json('Fail!', [], 400, $code['errors']);
        }

        return $this->responseService->json('Success!', [], 200);
    }

    public function verify(VerifyRequest $request)
    {
        $verify = $this->UserI->verify($request);

        if (!$verify) {
            return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.error')]);
        }

        if (!$verify['status']) {
            return $this->responseService->json('Fail!', [], 400, $verify['errors']);
        }

        $data = new UserMinifiedResource($verify['data']);
        $token = $verify['token'];
        return $this->responseService->json('Success!', ['token' => $token, 'user' => $data], 200);
    }

    public function socialCheck(SocialCheckRequest $request, $social)
    {
        switch ($social) {
            case 'facebook':
                $social_user = Facebook::userFromToken($request->access_token);
                break;
            
            case 'google':
                $social_user = Google::userFromToken($request->access_token);
                break;

            case 'twitter':
                $social_user = Twitter::userFromToken($request->access_token, $request->token_secret);
                break;
            
            case 'apple':
                $social_user = Apple::userFromToken($request->access_token);
                break;
            
            default:
                return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.providerNotAllowed', ['provider' => $social])]);
        }

        $user = $this->UserI->findBySocial($social, $social_user['id']);
        if (!$user) {
            $user_request = new CreateUserRequest([
                'username' => \Str::slug(implode('-', [$social_user['first_name'], $social_user['last_name'], time()])),
                'email' => $social_user['email'],
                'phone' => $social_user['phone'],
                'city' => 'riyadh',
                'birthday' => $social_user ? Carbon::parse($social_user['birthday']) : null,
                'user_type_class' => 1,
                $social.'_id' => $social_user['id'],
            ]);

            // validate
            if ($social_user['email']){
                $email_exists = $this->UserI->findByEmail($user_request->email);
                if ($email_exists) {
                    return $this->responseService->json('Fail!', ['error' => trans('messages.loginToConnect', ['attribute' => 'email'])], 400);
                }
            }

            if ($social_user['phone']) {
                $phone_exists = $this->UserI->findByPhone($user_request->phone);
                if ($phone_exists) {
                    return $this->responseService->json('Fail!', ['error' => trans('messages.loginToConnect', ['attribute' => 'phone'])], 400);
                }
            }

            if (!$social_user['first_name']) {
                return $this->responseService->json('Fail!', ['first_name' => trans('validation.required', ['attribute' => 'first_name'])], 400);
            }

            if (!$social_user['last_name']) {
                return $this->responseService->json('Fail!', ['last_name' => trans('validation.required', ['attribute' => 'last_name'])], 400);
            }

            return $this->signup($user_request);
        }

        $token = $this->UserI->createToken($request, $user);
        $user = new UserMinifiedResource($user);

        if (!$token) {
            return $this->responseService->json('Fail!', [], 400, $token['errors']);
        }

        return ['status' => true, 'data' => ['token' => $token, 'user' => $user]];
    }

    public function socialConnect(SocialCheckRequest $request, $social)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 401, ['error' => trans('message.unauthorized')]);
        }

        switch ($social) {
            case 'facebook':
                $social_user = Facebook::userFromToken($request->access_token);
                break;
            
            case 'google':
                $social_user = Google::userFromToken($request->access_token);
                break;

            case 'twitter':
                $social_user = Twitter::userFromToken($request->access_token, $request->token_secret);
                break;

            case 'apple':
                $social_user = Apple::userFromToken($request->access_token);
                break;
            
            default:
                return $this->responseService->json('Fail!', [], 400, ['error' => trans('messages.providerNotAllowed', ['provider' => $social])]);
        }

        $user = $this->UserI->findBySocial($social, $social_user['id']);

        if (!$user) {
            $user_request = new CreateUserRequest([
                $social.'_id' => $social_user['id'],
            ]);

            $user = $this->UserI->connectSocial($social, $user_request);
            if (!$user) {
                return $this->responseService->json('Fail!', [], 400, ['user' => [trans('crud.update', ['model' => trans_class_basename($this->UserI->model)])]]);
            }

            if (!$user['status']) {
                return $this->responseService->json('Fail!', [], 400, $user['errors']);
            }

            return $this->responseService->json('Success!', [], 200);
        }

        return $this->responseService->json('Fail!', ['error' => trans('messages.socialAlreadyConnectedToAnotherAccount', ['social' => trans('message.social.'.$social)])], 400);
    }

    public function socialDisconnect(Request $request, $social)
    {
        $user = $this->loggedinUser;

        if (!$user) {
            return $this->responseService->json('Fail!', [], 401, ['error' => trans('auth.forbidden')]);
        }

        $disconnect = $this->UserI->disconnectSocial($social);
        return $this->responseService->json('Success!', [], 200);
    }
}
