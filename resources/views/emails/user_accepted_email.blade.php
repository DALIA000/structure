@component('mail::message')
# Verify email

<p style="font-size: 1.25rem;">
	Welcome to {{ config('app.name') }}<br>
	Your account has been accepted.
</p>

Thank you for using our application!<br>
{{ config('app.name') }}

@component('mail::subcopy')
<div style="font-size: .75rem; text-align: center;">
	If you didn't signup to {{ config('app.name') }}, simply delete this email. You don't have to do anything. So that's easy.
</div>
@endcomponent

@endcomponent