@extends('dashboards.enforcer')

@section('title', 'Records')

@section('content')
<header>
    <h2>Records</h2>
</header>

<section class="table-container">
    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Plate</th>
                <th>Location</th>
                <th>Fine</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($records) && $records->count())
                @foreach($records as $r)
                    <tr>
                        <td>{{ $r->ticket_no ?? '-' }}</td>
                        <td>{{ $r->plate_no ?? '-' }}</td>
                        <td>{{ $r->location ?? '-' }}</td>
                        <td>{{ number_format($r->fine_amount ?? 0, 2) }}</td>
                        <td>
                            {{ ucfirst($r->status ?? 'pending') }}
                            @php
                                $actor = null;
                                if (class_exists(\App\Models\ActivityLog::class)) {
                                    $actor = \App\Models\ActivityLog::where('clamping_id', $r->id)->latest()->first();
                                }
                            @endphp
                            @if($actor)
                                <div class="actor">by {{ $actor->username }} <small>({{ optional($actor->created_at)->diffForHumans() }})</small></div>
                            @endif
                        </td>
                        <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button class="action-btn" data-id="{{ $r->id }}" data-action="accept">Accept</button>
                                <button class="action-btn" data-id="{{ $r->id }}" data-action="reject">Reject</button>
                                <button class="action-btn" data-id="{{ $r->id }}" data-action="approve">Approve</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="6">No records found.</td></tr>
            @endif
        </tbody>
    </table>

    @if(isset($records))
        <div style="margin-top:12px;">{{ $records->links() }}</div>
    @endif
</section>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('styles/records.css') }}">
@endpush
