@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="user-detail-page">
    <a href="{{ route('users') }}" class="btn">← Back to Users</a>

    <div class="user-card">
        <div class="user-header">
            <img src="{{ $user->details && $user->details->photo ? asset('storage/' . $user->details->photo) : asset('images/default-avatar.png') }}" alt="{{ $user->f_name }}">
            <h2>{{ $user->f_name }} {{ $user->l_name }}</h2>
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? '—' }}</p>
            <p><strong>Role:</strong> {{ $user->role->name ?? '—' }}</p>
            <p><strong>Status:</strong> {{ $user->status->status ?? '—' }}</p>
        </div>

        <div class="user-meta">
            <h3>Details</h3>
            @if($user->details)
                <p><strong>Address:</strong> {{ $user->details->address ?? '—' }}</p>
                <p><strong>Birthdate:</strong> {{ optional($user->details->birthdate)->format('Y-m-d') ?? '—' }}</p>
                <p><strong>Gender:</strong> {{ $user->details->gender ?? '—' }}</p>
            @else
                <p>No additional details.</p>
            @endif
        </div>

        <div class="user-actions" style="margin-top:12px;">
            @if(strtolower($user->status->status ?? '') === 'pending')
                <form id="approveForm" action="{{ route('users.approve', ['id' => $user->id]) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="auth-button">Approve</button>
                </form>

                <form id="rejectForm" action="{{ route('users.reject', ['id' => $user->id]) }}" method="POST" style="display:inline">
                    @csrf
                    <button class="auth-button" style="background:#c0392b">Reject</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
