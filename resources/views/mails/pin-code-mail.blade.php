<div style="text-align:right;direction:rtl;">
    @component('mail::message')
        <h2 style="text-align:right;direction:rtl;">رمز التحقق</h2>
        <h1 style="text-align:right;direction:rtl;">{{ $pin_code }}</h1>
        شكرا, <br>
    @endcomponent
    {{ config('app.name') }}
</div>
