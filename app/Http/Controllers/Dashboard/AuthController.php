<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Routing\Controller;
use App\Http\Resources\Dashboard\AdminResource;
use App\Http\Requests\Dashboard\AdminLoginRequest;
use App\Http\Requests\Dashboard\ConfirmTokenRequest;
use App\Http\Requests\Dashboard\AdminResetPasswordRequest;
use App\Http\Requests\Dashboard\UpdateAdminProfileRequest;
use App\Http\Requests\Dashboard\AdminConfirmPasswordRequest;

class AuthController extends Controller
{
    public function __construct(private \App\Http\Repositories\Auth\AuthInterface$modelInterface)
    {
        $this->modelInterface = $modelInterface;
    }

    public function profile()
    {
        $loggedinUser = app('loggedinUser');
        return responseJson(200, 'success', new AdminResource($loggedinUser));
    }

    public function login(AdminLoginRequest $request)
    {
        $auth = $this->modelInterface->login($request);
        if (!$auth) {
            return responseJson(400, __('auth.admin.failed'));
        }

        $token = $auth->createToken('admin')->accessToken;

        return responseJson(200, 'success', $token);
    }

    public function logout()
    {
        auth('admin')->user()->token()->revoke();

        return responseJson(200, 'success');
    }

    public function update(UpdateAdminProfileRequest $request)
    {
        $this->modelInterface->update($request);

        return responseJson(200, 'success');
    }

    public function resetPassword(AdminResetPasswordRequest $request)
    {
        $this->modelInterface->resetPassword($request);

        return responseJson(200, 'success');
    }

    public function pinCodeConfirmation(ConfirmTokenRequest $request)
    {
        $admin = $this->modelInterface->pinCodeConfirmation($request);
        if ($admin) {
            return responseJson(200, 'success', ['token' => $admin->token]);
        } else {
            return responseJson(400, __('auth.admin.failed'));
        }
    }

    public function confirmPassword(AdminConfirmPasswordRequest $request)
    {
        $status = $this->modelInterface->confirmPassword($request);
        if ($status) {
            return responseJson(200, 'success');
        } else {
            return responseJson(400, 'unsuccessful');
        }
    }

}
