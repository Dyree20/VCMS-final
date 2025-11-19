<p>Dear {{ $user->f_name }},</p>

<p>Your account status has been updated to <strong>{{ ucfirst($status) }}</strong>.</p>

@if($status === 'approved')
<p>You can now log in and use the system.</p>
@elseif($status === 'rejected')
<p>Your registration was rejected. Please contact support if you believe this is a mistake.</p>
@elseif($status === 'suspended')
<p>Your account has been suspended. Please contact support for more information.</p>
@endif

<p>Thank you,<br>The Admin Team</p>
