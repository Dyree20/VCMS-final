@php
    $user = auth()->user();
    $isPending = strtolower($user->status->status ?? 'active') === 'pending';
@endphp

@if($isPending)
    <div class="pending-account-banner">
        <i class="fas fa-clock"></i>
        <div class="pending-account-banner-content">
            <h4>Account Pending Approval</h4>
            <p>Your account is currently awaiting administrator approval. In the meantime, you have limited access:</p>
            <ul>
                <li>View and edit your profile information</li>
                <li>Manage your account settings and security</li>
                <li>Other features will be available once approved</li>
            </ul>
        </div>
    </div>
@endif
