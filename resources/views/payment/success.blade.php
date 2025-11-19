@extends('layouts.app')

@section('content')
<div class="payment-success">
    <h2>✅ Payment Successful!</h2>
    <p><strong>Ticket:</strong> {{ $ticket_no }}</p>
    <p><strong>Status:</strong> {{ ucfirst($status) }}</p>
    <a href="{{ route('dashboard') }}">Back to Dashboard</a>
</div>
@endsection
