@component('mail::message')
# Welcome to IDSC

Hello **{{ $employee->first_name }} {{ $employee->last_name }}**,

Welcome to IDSC — your account has been created for the staff portal.

**Portal URL:** [{{ config('app.url') }}/login]({{ config('app.url') }}/login)

@if($plainPassword)
**Your Portal Email:** `{{ $employee->email }}`  
**Temporary Password:** `{{ $plainPassword }}`

> Please log in and change your password right away.

@elseif($resetUrl)
We have emailed you a secure link to set your password. Click the button below to create your password.

@component('mail::button', ['url' => $resetUrl])
Set your password
@endcomponent
@endif

If you have any problems, contact HR.

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
