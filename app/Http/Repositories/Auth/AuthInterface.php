<?php

namespace App\Http\Repositories\Auth;

interface AuthInterface
{
    public function login($request);

    public function update($request);

    public function resetPassword($request);

    public function pinCodeConfirmation($request);

    public function confirmPassword($request);
}
