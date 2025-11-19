@extends('dashboards.enforcer')

@section('title', 'Notifications')

@section('content')
@php
    $user = auth()->user();
    $user->load('details');
    $profilePhoto = $user->details && $user->details->photo 
        ? asset('storage/' . $user->details->photo) 
        : asset('images/default-avatar.png');
@endphp
<header>
    <h2>Notifications</h2>
    <a href="{{ route('enforcer.profile') }}" class="profile-link" title="View Profile">
        <img src="{{ $profilePhoto }}" alt="Profile" class="profile-picture" id="headerProfilePic">
    </a>
</header>

<section class="entries">
    @if(isset($myClampings) && $myClampings->count() > 0)
        @foreach($myClampings as $clamping)
            @php
                $clampingActivities = $activities[$clamping->id] ?? collect();
                $latestActivity = $clampingActivities->first();
            @endphp
            
            <div class="entry">
                <div class="entry-left">
                    @if($clamping->status === 'paid')
                        <i class="fa-solid fa-check-circle" style="color: #28a745;"></i>
                    @elseif($clamping->status === 'approved')
                        <i class="fa-solid fa-check" style="color: #007bff;"></i>
                    @elseif($clamping->status === 'accepted')
                        <i class="fa-solid fa-thumbs-up" style="color: #17a2b8;"></i>
                    @elseif($clamping->status === 'rejected')
                        <i class="fa-solid fa-times-circle" style="color: #dc3545;"></i>
                    @else
                        <i class="fa-solid fa-bell" style="color: #ffc107;"></i>
                    @endif
                    
                    <div class="entry-info">
                        <h4>{{ $clamping->ticket_no }}</h4>
                        <p>
                            @if($latestActivity)
                                {{ ucfirst($latestActivity->action) }} by {{ $latestActivity->user ? ($latestActivity->user->f_name . ' ' . $latestActivity->user->l_name) : ($latestActivity->username ?? 'Unknown') }}
                                <small>({{ $latestActivity->created_at->diffForHumans() }})</small>
                            @else
                                {{ $clamping->created_at->diffForHumans() }} • {{ $clamping->location ?? '-' }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="entry-right">
                    <p>
                        <span class="status-badge status-{{ strtolower($clamping->status) }}">
                            {{ strtoupper($clamping->status) }}
                        </span>
                    </p>
                    <small style="color: #888;">₱{{ number_format($clamping->fine_amount, 2) }}</small>
                </div>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <i class="fa-solid fa-bell-slash" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
            <p>No notifications yet.</p>
        </div>
    @endif
</section>

<style>
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-paid {
        background: #d4edda;
        color: #155724;
    }
    
    .status-approved {
        background: #cce5ff;
        color: #004085;
    }
    
    .status-accepted {
        background: #d1ecf1;
        color: #0c5460;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('styles/notifications.css') }}">
@endpush
