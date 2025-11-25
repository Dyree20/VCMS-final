@extends('layouts.app')

@section('title', 'Violations')

@section('content')
<div class="clamping-container">
    <h2 class="page-title">Clamping Records</h2>

    <!-- Quick Actions -->
    <div class="actions-bar">
        <button class="btn btn-primary" id="addBtn"><i class="fa-solid fa-plus" style="margin-right: 6px;"></i>Add New Clamping</button>
        <!-- Search & Filter -->
        <form method="GET" action="{{ route('clampings') }}" class="search-filter">
            <input type="text" name="search" class="input-text" placeholder="Search by Plate / Ticket No." value="{{ request('search') }}">
            <select name="status" class="select-box">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
            </select>
            <input type="date" name="date_from" class="input-text" value="{{ request('date_from') }}" placeholder="From Date">
            <input type="date" name="date_to" class="input-text" value="{{ request('date_to') }}" placeholder="To Date">
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                <a href="{{ route('clampings') }}" class="btn btn-secondary" style="background: #6c757d;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Violations Table -->
    <div class="table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket No.</th>
                    <th>Plate No.</th>
                    <th>Attending Officer</th>
                    <th>Reason for Clamping</th>
                    <th>Location</th>
                    <th>Date Clamped</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
                <tbody>
                @foreach($clampings as $index => $clamping)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $clamping->ticket_no }}</td>
                        <td>{{ $clamping->plate_no }}</td>
                        <td>
                            <span style="font-weight: 600; color: #007bff;">
                                {{ $clamping->user ? $clamping->user->f_name . ' ' . $clamping->user->l_name : 'N/A' }}
                            </span>
                            <div style="font-size: 12px; color: #999;">
                                {{ $clamping->user ? $clamping->user->enforcer_id : '—' }}
                            </div>
                        </td>
                        <td>{{ $clamping->reason }}</td>
                        <td>{{ $clamping->location }}</td>
                        <td>{{ $clamping->date_clamped }}</td>
                        <td>
                            @php
                                $status = strtolower($clamping->status ?? 'unknown');

                                // map database statuses to CSS classes
                                $statusClass = match($status) {
                                    'paid' => 'active',
                                    'pending' => 'probation',
                                    'released' => 'inactive',
                                    'cancelled' => 'cancelled',
                                    default => '',
                                };
                            @endphp
                            <span class="status {{ $statusClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" title="View Details" onclick="viewClamping({{ $clamping->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                                <button class="btn-action btn-edit" title="Edit" onclick="editClamping({{ $clamping->id }})">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" title="Delete" onclick="deleteClamping({{ $clamping->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($clampings->hasPages())
    <div style="display: flex; justify-content: center; margin-top: 30px;">
        {{ $clampings->links() }}
    </div>
    @endif

@include('partials.add-clamping')

<script>
    const addBtn = document.getElementById('addBtn');
    const closeBtn = document.getElementById('closeBtn');
    const panel = document.getElementById('addPanel');

    addBtn.addEventListener('click', () => {
        panel.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
        panel.classList.add('hidden');
    });

    // View Clamping Details
    function viewClamping(clampingId) {
        window.location.href = `/clampings/${clampingId}`;
    }

    // Edit Clamping
    function editClamping(clampingId) {
        window.location.href = `/clampings/${clampingId}/edit`;
    }

    // Delete Clamping with Confirmation
    function deleteClamping(clampingId) {
        if (confirm('Are you sure you want to delete this clamping record?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/clampings/${clampingId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

@endsection
