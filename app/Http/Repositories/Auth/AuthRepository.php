<?php

namespace App\Http\Repositories\Auth;

use App\Models\Admin;

class AuthRepository implements AuthInterface
{
    private $model;

    public function __construct(Admin $model)
    {
        $this->model = $model;
    }

    public function login($request)
    {
        $model = $this->model->where('email', $request->email)->first();
        if ($model) {
            if (\Illuminate\Support\Facades\Hash::check($request->password, $model->password)) {
                return $model;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function update($request)
    {
        user()->update(array_filter($request->all()));

    }

    public function resetPassword($request)
    {
        $pin_code = rand(111111, 999999);
        $model = $this->model->where('email', $request->email)->first();
        $model->update(['pin_code' => $pin_code]);
        $model->refresh();
        dispatch(new \App\Jobs\SendMail($model->email, $model->pin_code));
    }

    public function pinCodeConfirmation($request)
    {
        $model = $this->model->where(['pin_code' => $request->pin_code, 'email' => $request->email])->first();
        if ($model) {
            $model->update([
                'pin_code' => null,
                'token' => \Illuminate\Support\Str::random(60),
            ]);

            return $model;
        } else {
            return false;
        }
    }

    public function confirmPassword($request)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $model = $this->model->where('token', $request->token)->where('token', '!=', null)->first();
            if ($model) {
                $model->update(['token' => null, 'password' => $request->password]);
                return true;

            } else {
                return false;
            }});
    }
}
