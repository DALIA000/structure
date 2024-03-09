@component('mail::message')
# Email verified successfully.

<p style="font-size: 1.25rem;">
	Welcome to {{ config('app.name') }}<br>
	Your account has been activated.<br>
	If you need any help, Please contact us at <a href="mailto:{{ config('app.email') }}">{{ config('app.email') }}</a>. 
</p>

Thank you for using our application!<br>
{{ config('app.name') }}

@component('mail::subcopy')
<div style="font-size: .75rem; text-align: center;">
	If you didn't signup to {{ config('app.name') }}, simply delete this email. You don't have to do anything. So that's easy.
</div>
@endcomponent

@endcomponent