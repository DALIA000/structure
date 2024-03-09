@component('mail::message')
# Verify email

<p style="font-size: 1.25rem;">
	Welcome to {{ config('app.name') }}<br>
	One step and you are in.
</p>

<div style="background-color: #eee;padding: 1rem;text-align: center;font-size: 1.25rem;font-weight: 500; margin-bottom: 1rem;"> 
	{{ $model->verification_code?->code }} 
</div> 

Thank you for using our application!<br>
{{ config('app.name') }}

@component('mail::subcopy')
<div style="font-size: .75rem; text-align: center;">
	If you didn't signup to {{ config('app.name') }}, simply delete this email. You don't have to do anything. So that's easy.
</div>
@endcomponent

@endcomponent