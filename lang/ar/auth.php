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

    'failed' => 'البيانات التى أدخلتها غير صحيحة!', 
    'admin.failed' => 'البيانات التى أدخلتها غير صحيحة!',
    'notVerified' => 'الحساب الخاص بك غير مفعل! رجاء قم بتفعيل الحساب أولا.',
    'blocked' => 'هذا الحساب تم حذره بواسطة ' . config('app.name') . '.',
    'password' => 'كلمة المرور غير صحيحة!',
    'throttle' => 'تعديت الحد الأقصى من المحاولات! حاول مرة أخرى بعد  :seconds ثوان.',

    'signup' => 'تم ارسال رابط تفعيل الحساب إلى البريد الإلكتروني الخاص بك. يمكنك تسجيل الدخول بعد تأكيد البريد الإلكتروني.',
    'requireAcceptAction' => 'لم يتم قبول طلب التسجيل الخاص بك!',
    'forbidden' => 'غير مصرح لك بتنفيذ هذا الأمر.',
    'logout' => [
        'successed' => 'تم تسجيل الخروج بنجاح.',
        'failed' => 'لم يتم تسجيل الخروج.',
    ],
    'current_password' => 'كلمة المرور الحالية غير صحيحة!',
    'invalid' => ':attribute الذي أدخلته غير صحيح.',
    'login' => 'يجب تسجيل الدخول أولاً.',
    'loginToVerify' => ':attribute مستخدم بالفعل, رجاءً قم بتسجيل الدخول لربط الحساب مع :social.',
    'cascade_delete' => 'هذا العنصر مرتبط بـ :count :cascade.',
    'socialAlreadyConnected' => 'حساب :social مرتبط بالفعل بحساب آخر لدى ' .  config('app.name') . '.',
];
