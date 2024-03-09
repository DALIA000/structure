<?php

$validation = [

  /*
  |--------------------------------------------------------------------------
  | Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | The following language lines contain the default error messages used by
  | the validator class. Some of these rules have multiple versions such
  | as the size rules. Feel free to tweak each of these messages here.
  |
  */

  'accepted' => 'يجب قبول الحقل :attribute',
  'accepted_if' => 'الحقل :attribute مقبول في حال ما إذا كان :other يساوي :value.',
  'active_url' => 'الحقل :attribute لا يمثل رابطًا صحيحًا',
  'after' => 'يجب على الحقل :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
  'after_or_equal' => 'الحقل :attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
  'alpha' => 'يجب أن لا يحتوي الحقل :attribute سوى على حروف',
  'alpha_dash' => 'يجب أن لا يحتوي الحقل :attribute على حروف، أرقام ومطّات.',
  'alpha_num' => 'يجب أن يحتوي :attribute على حروف وأرقام فقط',
  'array' => 'يجب أن يكون الحقل :attribute مصفوفة',
  'before' => 'يجب على الحقل :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
  'before_or_equal' => 'الحقل :attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
  'between' => [
    'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
    'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
    'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
    'string' => 'يجب أن يكون عدد حروف النص :attribute بين :min و :max',
  ],
  'boolean' => 'يجب أن تكون قيمة الحقل :attribute إما true أو false ',
  'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute',
  'current_password' => 'كلمة المرور الحالية غير صحيحة',
  'date' => 'الحقل :attribute ليس تاريخًا صحيحًا',
  'date_equals' => 'لا يتساوى الحقل :attribute مع :date.',
  'date_format' => 'لا يتوافق الحقل :attribute مع الشكل :format.',
  'declined' => 'يجب رفض الحقل :attribute',
  'declined_if' => 'الحقل :attribute مرفوض في حال ما إذا كان :other يساوي :value.',
  'different' => 'يجب أن يكون الحقلان :attribute و :other مختلفان',
  'digits' => 'يجب أن يحتوي الحقل :attribute على :digits رقمًا / أرقام',
  'digits_between' => 'يجب أن يحتوي الحقل :attribute بين :min و :max رقمًا / أرقام',
  'dimensions' => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
  'distinct' => 'للحقل :attribute قيمة مكررة.',
  'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
  'ends_with' => 'الـ :attribute يجب ان ينتهي بأحد القيم التالية :value.',
  'enum' => 'الحقل :attribute غير صحيح',
  'exists' => 'قيمة الحقل :attribute غير متاحة',
  'file' => 'الـ :attribute يجب أن يكون من ملفا.',
  'filled' => 'الحقل :attribute إجباري',
  'gt' => [
    'array' => 'الـ :attribute يجب ان يحتوي علي اكثر من :value عناصر / عنصر.',
    'file' => 'الـ :attribute يجب ان يكون اكبر من :value كيلو بايت.',
    'numeric' => 'الـ :attribute يجب ان يكون اكبر من :value.',
    'string' => 'الـ :attribute يجب ان يكون اكبر من :value حروف / حرفاً.',
  ],
  'gte' => [
    'array' => 'الـ :attribute يجب ان يحتوي علي :value عناصر / عنصر أو اكثر.',
    'file' => 'الـ :attribute يجب ان يكون اكبر من أو يساوي :value كيلو بايت.',
    'numeric' => 'الـ :attribute يجب ان يكون اكبر من أو يساوي :value.',
    'string' => 'الـ :attribute يجب ان يكون اكبر من أو يساوي :value حروف / حرفًا.',
  ],
  'image' => 'يجب أن يكون الحقل :attribute صورة',
  'in' => ':attribute غير موجود / موجودة',
  'in_array' => 'الحقل :attribute غير موجود / موجودة في :other.',
  'integer' => 'يجب أن يكون الحقل :attribute عددًا صحيحًا',
  'ip' => 'يجب أن يكون الحقل :attribute عنوان IP صحيح البُنية',
  'ipv4' => 'يجب أن يكون الحقل :attribute عنوان IPv4 صحيح البنية صحيحة.',
  'ipv6' => 'يجب أن يكون الحقل :attribute عنوان IPv6 صحيح البنية صحيحة.',
  'json' => 'يجب أن يكون الحقل :attribute نصا من نوع JSON.',
  'lt' => [
    'array' => 'الـ :attribute يجب ان يحتوي علي اقل من :value عناصر / عنصر.',
    'file' => 'الـ :attribute يجب ان يكون اقل من :value كيلو بايت.',
    'numeric' => 'الـ :attribute يجب ان يكون اقل من :value.',
    'string' => 'الـ :attribute يجب ان يكون اقل من :value حروف / حرفًا.',
  ],
  'lte' => [
    'array' => 'الـ :attribute يجب ان يحتوي علي اكثر من :value عناصر / عنصر.',
    'file' => 'الـ :attribute يجب ان يكون اقل من أو يساوي :value كيلو بايت.',
    'numeric' => 'الـ :attribute يجب ان يكون اقل من أو يساوي :value.',
    'string' => 'الـ :attribute يجب ان يكون اقل من أو يساوي :value حروف / حرفًا.',
  ],
  'mac_address' => 'يجب أن يكون الحقل :attribute عنوان MAC صحيح البنية صحيحة.',
  'max' => [
    'array' => 'يجب أن لا يحتوي الحقل :attribute على أكثر من :max عناصر / عنصر.',
    'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
    'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أصغر لـ :max.',
    'string' => 'يجب أن لا يتجاوز طول نص :attribute :max حروف / حرفًا',
  ],
  'mimes' => 'يجب أن يكون الحقل ملفًا من نوع : :values.',
  'mimetypes' => 'يجب أن يكون الحقل ملفًا من نوع : :values.',
  'min' => [
    'array' => 'يجب أن يحتوي الحقل :attribute على الأقل على :min عنصر/ عناصر',
    'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
    'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية أو أكبر لـ :min.',
    'string' => 'يجب أن يكون طول نص :attribute على الأقل :min حروف / حرفاً',
  ],
  'multiple_of' => 'يجب أن يحتوي الحقل :attribute على أكثر من قيمة :value.',
  'not_in' => ':attribute غير موجود / موجودة',
  'not_regex' => ':attribute نوعه غير موجود',
  'numeric' => 'يجب على الحقل :attribute أن يكون رقمًا',
  'password' => 'كلمة المرور غير صحيحة.',
  'present' => 'يجب تقديم الحقل :attribute',
  'prohibited' => 'الحقل :attribute محظور',
  'prohibited_if' => 'الحقل :attribute محظور في حال ما إذا كان :other يساوي :value.',
  'prohibited_unless' => 'الحقل :attribute محظور في حال ما لم يكون :other يساوي :value.',
  'prohibits' => 'الحقل :attribute يحظر :other الحقل',
  'regex' => 'صيغة الحقل :attribute .غير صحيحة',
  'required' => 'الحقل :attribute مطلوب.',
  'required_array_keys' => 'الحقل :attribute يجب ان يحتوي علي مدخلات للقيم التالية :values.',
  'required_if' => 'الحقل :attribute مطلوب في حال ما إذا كان :other يساوي :value.',
  'required_unless' => 'الحقل :attribute مطلوب في حال ما لم يكن :other يساوي :values.',
  'required_with' => 'الحقل :attribute إذا توفر :values.',
  'required_with_all' => 'الحقل :attribute إذا توفر :values.',
  'required_without' => 'الحقل :attribute إذا لم يتوفر :values.',
  'required_without_all' => 'الحقل :attribute إذا لم يتوفر :values.',
  'same' => 'يجب أن يتطابق الحقل :attribute مع :other',
  'size' => [
    'array' => 'يجب أن يحتوي الحقل :attribute على :size عنصر/ عناصر بالظبط',
    'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
    'numeric' => 'يجب أن تكون قيمة الحقل :attribute مساوية لـ :size',
    'string' => 'يجب أن يحتوي النص :attribute على :size حروف / حرفاً بالظبط',
  ],
  'starts_with' => 'الحقل :attribute يجب ان يبدأ بأحد القيم التالية: :values.',
  'string' => 'يجب أن يكون الحقل :attribute نصاً.',
  'timezone' => 'يجب أن يكون :attribute نطاقاً زمنياً صحيحاً',
  'unique' => 'قيمة الحقل \':attribute\' مُستخدمة من قبل',
  'uploaded' => 'فشل في تحميل الـ :attribute',
  'url' => 'صيغة :attribute غير صحيحة',
  'uuid' => 'الحقل :attribute يجب ان ايكون رقم UUID صحيح.',

  'email_regex' => 'الحقل :attribute يجب أن يكون بريد الكتروني صحيح البنية.',
  'password_regex' => 'يجب أن تحتوي كلمة المرور على الأقل 8 أحرف قد تحتوي على: على الأقل حرف واحد كبير, حرف واحد صغير, رقم, حرف خاص.',
  'slug' => 'الحقل :attribute يمكن أن يحتوي على a-z, 0-9 فقط.',
  'email_or_phone' => 'الحقل :attribute يجب أن يكون اما بريد الكتروني صحيح البنية أو رقم هاتف صحيح',
  'trade_license' => 'الحقل :attribute يجب أن يحتوي فقط على أرقام 0-9.',
  'model_exists' => 'قيمة الحقل :attribute غير متاحة',
  'coupon' => 'الحقل :attribute يمكن أن يحتوي فقط على a-z, A-Z, 0-9, -.',
  'phone_number' => ':attribute غير صحيح.',
  'alpha_spaceable' => ':attribute غير صحيح, :attribute يمكن أن يحتوي فقط على a-z, A-Z, - ومسافات.',
  /*
  |--------------------------------------------------------------------------
  | Custom Validation Language Lines
  |--------------------------------------------------------------------------
  |
  | Here you may specify custom validation messages for attributes using the
  | convention "attribute.rule" to name the lines. This makes it quick to
  | specify a specific custom language line for a given attribute rule.
  |
  */

  'custom' => [
    'IsInWebsiteLanguages' => 'اللغة غير متاحة',
    'UserExists' => 'رقم الهاتف أو البريد الإلكتروني غير صحيح',
  ],

  /*
  |--------------------------------------------------------------------------
  | Custom Validation Attributes
  |--------------------------------------------------------------------------
  |
  | The following language lines are used to swap our attribute placeholder
  | with something more reader friendly such as "E-Mail Address" instead
  | of "email". This simply helps us make our message more expressive.
  |
  */

  'attributes' => [
    'login' => 'البريد الإلكتروني / الهاتف',
    'name' => 'الاسم',
    'username' => 'اسم المُستخدم',
    'email' => 'البريد الالكتروني',
    'first_name' => 'الاسم',
    'last_name' => 'اسم العائلة',
    'password' => 'كلمة المرور',
    'password_confirmation' => 'تأكيد كلمة المرور',
    'city' => 'المدينة',
    'country' => 'الدولة',
    'address' => 'العنوان',
    'phone' => 'الهاتف',
    'whatsapp' => 'واتساب',
    'video_link' => 'رابط الفيديو',
    'mobile' => 'الجوال',
    'subject' => 'العنوان',
    'message' => 'الرسالة',
    'age' => 'العمر',
    'sex' => 'الجنس',
    'gender' => 'النوع',
    'day' => 'اليوم',
    'month' => 'الشهر',
    'year' => 'السنة',
    'hour' => 'ساعة',
    'minute' => 'دقيقة',
    'second' => 'ثانية',
    'content' => 'المُحتوى',
    'description' => 'الوصف',
    'excerpt' => 'الملخص',
    'date' => 'التاريخ',
    'time' => 'الوقت',
    'available' => 'متاح',
    'size' => 'الحجم',
    'price' => 'السعر',
    'desc' => 'نبذة',
    'title' => 'العنوان',
    'q' => 'البحث',
    'link' => 'رابط',
    'slug' => 'اختصار الاسم',

    'role' => 'رتبة',
    'image' => 'صورة',
    'status' => 'الحالة',
    'pin' => 'رمز التأكيد',
    'company' => 'شركة',

    'permissions.*' => 'تصريح',
    'phones.*' => 'الهاتف',
    'images.*' => 'الصورة',

    'locale' => 'اللغة',
    'locales.*.locale' => 'اللغة',
    'locales.*.name' => 'الاسم',
    'locales.*.description' => 'الوصف',

    'property_type' => 'قسم الميزة',

    'years' => 'سنين',
    'months' => 'شهور',
    'days' => 'أيام',

    'business_name' => 'الإسم التجاري',

    'Admin' => 'مدير',
    'city' => 'مدينة',
    'contact' => 'اتصل بنا',
    'country' => 'دولة',
    'currency' => 'عملة',
    'image' => 'صورة',
    'images' => 'صور',
    'language' => 'لغة',
    'locale' => 'لغة',
    'permission' => 'صلاحية',
    'plan' => 'باقة',
    'reply' => 'رد',
    'role' => 'رتبة',
    'setting' => 'إعدادات',
    'social' => 'منصة تواصل',
    'subscription' => 'اشتراك',
    'promotion' => 'ترقية',
    'advertisment' => 'إعلان مدفوع',

    'facebook' => 'فيسبوك',
    'instagram' => 'انستجرام',
    'twitter' => 'تويتر',
    'snapchat' => 'سناب شات',
    'tiktok' => 'تيك توك',
    'youtube' => 'يوتيوب',

    'terms_and_conditions' => 'الشروط والأحكام',
    'valid_data' => 'معلومات صحيحة',
    'spam_option_id' => 'سبب التبليغ',
    'group_discount' => 'خصم خاص بالأكاديميات',
    'seats_count' => 'عدد المقاعد',
  ],

];

return $validation;
