@component('mail::message')
# Account rejected

<p style="font-size: 1.25rem;">
	Welcome to {{ config('app.name') }}<br>
	We ar sorry, your account has been rejected for the upcomming reasons.
    please read them carefully and do the changes to continue your account verification.
</p>

<p style="font-size: 1.25rem;">
	{{$message}}
</p>

Thank you for using our application!<br>
{{ config('app.name') }}

@component('mail::subcopy')
<div style="font-size: .75rem; text-align: center;">
	If you didn't signup to {{ config('app.name') }}, simply delete this email. You don't have to do anything. So that's easy.
</div>
@endcomponent

@endcomponent