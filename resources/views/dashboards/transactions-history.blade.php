@extends('dashboards.enforcer')

@section('content')
<div class="section-container">
    <a href="{{ route('enforcer.profile') }}" class="back-btn">← Back to Profile</a>
    
    <h2>Transactions History</h2>
    
    @if($transactions->count() > 0)
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                    <td>{{ $transaction->type ?? 'Payment' }}</td>
                    <td>₱{{ number_format($transaction->amount ?? 0, 2) }}</td>
                    <td><span class="badge">{{ $transaction->status ?? 'Completed' }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; padding: 40px 20px; color: #999;">No transactions found.</p>
    @endif
</div>

<style>
    .section-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
    }
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        color: #4e5de3;
        text-decoration: none;
        font-weight: 600;
    }
    .transactions-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .transactions-table th, .transactions-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .transactions-table th {
        background: #f5f5f5;
        font-weight: 600;
        color: #333;
    }
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        background: #e3f2fd;
        color: #1976d2;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endsection
