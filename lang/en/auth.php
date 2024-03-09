<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Invalid email / phone or password!',
    'admin.failed' => 'Invalid email or password!',
    'notVerified' => 'Your account is not verified! Please verify your account first.',
    'blocked' => 'This account has been block by ' . env('app.name'),
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'signup' => 'Signed up successfully. You can sign in after your request got accepted.',
    'requireAcceptAction' => 'Your signup request is pending!',
    'forbidden' => 'You are not allowed to do this action.',
    'logout' => [
        'successed' => 'Logged out successfully.',
        'failed' => 'Failed to logout.',
    ],
    'current_password' => 'Current password is incorrect.',
    'invalid' => 'The :attribute you\'ve entered is invalid.',
    'login' => 'You need to login first.',
    'loginToVerify' => 'This :attribute has been taken, Please login to connect your account with :social.',
    'cascade_delete' => 'this :model is assigned to  :count :cascade.',
    'socialAlreadyConnected' => 'This :social account is already connected to another ' .  config('app.name') . ' account.',
];
