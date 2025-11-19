@extends('layouts.app')

@section('title', 'Appeal Details')

@section('content')
<div class="appeal-detail-container">
    <!-- Header -->
    <div class="appeal-detail-header">
        <a href="{{ route('appeals.index') }}" class="appeal-detail-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="appeal-detail-title-section">
            <h1 class="appeal-detail-title">Appeal #{{ $appeal->id }}</h1>
            <p class="appeal-detail-subtitle">Ticket: {{ $appeal->clamping->ticket_no }}</p>
        </div>
        <span class="appeal-status-badge {{ strtolower(str_replace('_', '-', $appeal->status)) }}">
            {{ ucfirst(str_replace('_', ' ', $appeal->status)) }}
        </span>
    </div>

    <!-- Content Grid -->
    <div class="appeal-detail-grid">
        <!-- Appellant Information -->
        <div class="appeal-detail-card">
            <h3 class="appeal-card-title">
                <i class="fa-solid fa-user"></i>
                Appellant Information
            </h3>
            <div class="appeal-info-field">
                <label>Name</label>
                <p>{{ $appeal->user->f_name }} {{ $appeal->user->l_name }}</p>
            </div>
            <div class="appeal-info-field">
                <label>Email</label>
                <p>{{ $appeal->user->email }}</p>
            </div>
            <div class="appeal-info-field">
                <label>Phone</label>
                <p>{{ $appeal->user->phone ?? 'N/A' }}</p>
            </div>
            <div class="appeal-info-field">
                <label>Submitted On</label>
                <p>{{ $appeal->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        <!-- Clamping Details -->
        <div class="appeal-detail-card">
            <h3 class="appeal-card-title">
                <i class="fa-solid fa-car"></i>
                Clamping Details
            </h3>
            <div class="appeal-info-field">
                <label>Ticket Number</label>
                <p><strong>{{ $appeal->clamping->ticket_no }}</strong></p>
            </div>
            <div class="appeal-info-field">
                <label>Vehicle Plate</label>
                <p>{{ $appeal->clamping->plate }}</p>
            </div>
            <div class="appeal-info-field">
                <label>Fine Amount</label>
                <p class="fine-amount">₱{{ number_format($appeal->clamping->fine, 2) }}</p>
            </div>
            <div class="appeal-info-field">
                <label>Clamping Date</label>
                <p>{{ $appeal->clamping->created_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Appeal Details -->
    <div class="appeal-detail-card full-width">
        <h3 class="appeal-card-title">
            <i class="fa-solid fa-message"></i>
            Appeal Details
        </h3>
        <div class="appeal-info-field">
            <label>Reason</label>
            <p><strong>{{ $appeal->reason }}</strong></p>
        </div>
        <div class="appeal-info-field">
            <label>Description</label>
            <div class="appeal-description">{{ $appeal->description }}</div>
        </div>
    </div>

    <!-- Resolution (if applicable) -->
    @if($appeal->resolved_at)
    <div class="appeal-detail-card full-width">
        <h3 class="appeal-card-title">
            <i class="fa-solid fa-check-circle"></i>
            Resolution Details
        </h3>
        <div class="appeal-info-field">
            <label>Resolved By</label>
            <p>{{ $appeal->resolvedBy->f_name }} {{ $appeal->resolvedBy->l_name }}</p>
        </div>
        <div class="appeal-info-field">
            <label>Resolution Date</label>
            <p>{{ $appeal->resolved_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="appeal-info-field">
            <label>Resolution Notes</label>
            <div class="appeal-notes">{{ $appeal->resolution_notes }}</div>
        </div>
    </div>
    @endif

    <!-- Update Status Form (if not resolved) -->
    @if($appeal->status !== 'approved' && $appeal->status !== 'rejected')
    <div class="appeal-detail-card full-width">
        <h3 class="appeal-card-title">
            <i class="fa-solid fa-edit"></i>
            Update Status
        </h3>
        <form action="{{ route('appeals.update-status', $appeal) }}" method="POST" class="appeal-form">
            @csrf
            
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="appeal-select" required>
                    <option value="">-- Select Status --</option>
                    <option value="under_review" {{ $appeal->status === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>

            <div class="form-group">
                <label for="resolution_notes">Resolution Notes</label>
                <textarea name="resolution_notes" id="resolution_notes" class="appeal-textarea" rows="5" placeholder="Enter your resolution notes..."></textarea>
            </div>

            <div class="appeal-form-actions">
                <button type="submit" class="appeal-btn appeal-btn-primary">
                    <i class="fa-solid fa-save"></i>
                    Update Status
                </button>
                <a href="{{ route('appeals.index') }}" class="appeal-btn appeal-btn-secondary">
                    <i class="fa-solid fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
    @endif
</div>

<link rel="stylesheet" href="{{ asset('styles/appeals-detail.css') }}">

@endsection
