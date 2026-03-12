@component('mail::message')
# Welcome to {{ config('app.name') }}!

Hi **{{ $user->name }}**,

Your account has been successfully created by an administrator. Below are your login credentials:

@component('mail::table')
| | |
|:---|:---|
| **Name:** | {{ $user->name }} |
| **Email:** | {{ $user->email }} |
| **Password:** | {{ $plainPassword }} |
| **Role:** | {{ $role }} |
| **Position:** | {{ $user->position }} |
| **Status:** | {{ ucfirst($user->status) }} |
@endcomponent

@component('mail::panel')
**Important:** Please change your password after your first login for security purposes.
@endcomponent

@component('mail::button', ['url' => url('/login')])
Login to Your Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@slot('subcopy')
This is an automated email from the {{ config('app.name') }} system. Please do not reply to this email. If you did not expect this account, please contact your administrator.
@endslot
@endcomponent
