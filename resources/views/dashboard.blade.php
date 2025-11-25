@extends('layouts.app')

@section('title', 'Clamping Dashboard')

@section('content')
<div class="dashboard">
    <div class="wrapper">
        <!-- Top Header -->
        <h2>Hi, {{ auth()->user()->f_name }}</h2>
        <p>Here's an overview of today's clamping reports</p>

        <div class="reports-container">
            <!-- Stats Cards -->
            <div class="reports">
                <div class="card">
                    <i class='bx bx-car'></i>
                    <h3>{{ number_format($totalClampings) }}</h3>
                    <p>Total Clamped Vehicles</p>
                    @if($clampingTrend['type'] === 'up')
                        <span class="trend up">+{{ $clampingTrend['value'] }}%</span>
                    @elseif($clampingTrend['type'] === 'down')
                        <span class="trend down">-{{ $clampingTrend['value'] }}%</span>
                    @else
                        <span class="trend neutral">0%</span>
                    @endif
                </div>
                <div class="card">
                    <i class='bx bx-error'></i>
                    <h3>{{ number_format($activeViolations) }}</h3>
                    <p>Active Violations</p>
                    @if($activeTrend['type'] === 'up')
                        <span class="trend up">+{{ $activeTrend['value'] }}%</span>
                    @elseif($activeTrend['type'] === 'down')
                        <span class="trend down">-{{ $activeTrend['value'] }}%</span>
                    @else
                        <span class="trend neutral">0%</span>
                    @endif
                </div>
                <div class="card">
                    <i class='bx bx-money'></i>
                    <h3>₱{{ number_format($totalCollected, 2) }}</h3>
                    <p>Total Collected Fines</p>
                    @if($collectedTrend['type'] === 'up')
                        <span class="trend up">+{{ $collectedTrend['value'] }}%</span>
                    @elseif($collectedTrend['type'] === 'down')
                        <span class="trend down">-{{ $collectedTrend['value'] }}%</span>
                    @else
                        <span class="trend neutral">0%</span>
                    @endif
                </div>
            </div>

            <!-- Chart Section -->
            <div class="chart-section">
                <h4>Violations per Day</h4>
                <div class="chart-container">
                    <canvas id="violationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="enforcers">
        <h3>Enforcers</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Enforcer Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Online Status</th>
                        <th>Total Clampings</th>
                        <th>Assigned Area</th>
                        <th>Enforcer ID</th>
                    </tr>
                </thead>
                <tbody>
                    @if($enforcers->count() > 0)
                        @foreach($enforcers as $enforcer)
                            <tr>
                                <td>{{ $enforcer->f_name }} {{ $enforcer->l_name }}</td>
                                <td>{{ $enforcer->email }}</td>
                                <td>
                                    @php
                                        $statusClass = strtolower($enforcer->status->status ?? 'pending');
                                        $statusClass = match($statusClass) {
                                            'approved' => 'active',
                                            'pending' => 'probation',
                                            'suspended' => 'inactive',
                                            default => 'inactive',
                                        };
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $enforcer->status->status ?? 'Pending' }}</span>
                                </td>
                                <td>
                                    <div class="online-status-badge" style="display: flex; align-items: center; gap: 8px;">
                                        <span class="online-dot" style="
                                            width: 10px;
                                            height: 10px;
                                            border-radius: 50%;
                                            background-color: {{ $enforcer->is_online ? '#28a745' : '#dc3545' }};
                                            display: inline-block;
                                            animation: {{ $enforcer->is_online ? 'pulse 2s infinite' : 'none' }};
                                        "></span>
                                        <span style="font-weight: 600; color: {{ $enforcer->is_online ? '#28a745' : '#dc3545' }}; display: flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-circle" style="font-size: 10px;"></i>{{ $enforcer->is_online ? 'Online' : 'Offline' }}
                                        </span>
                                        <span style="font-size: 12px; color: #999; margin-left: 8px;">
                                            {{ $enforcer->last_seen }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $enforcer->clampings_count }}</td>
                                <td>
                                    @if($enforcer->parkingZone)
                                        <span style="background: #e8f4f8; padding: 4px 8px; border-radius: 4px; color: #0c5577;">
                                            {{ $enforcer->parkingZone->name }}
                                        </span>
                                    @else
                                        <span style="color: #999;">Not Assigned</span>
                                    @endif
                                </td>
                                <td>{{ $enforcer->enforcer_id ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px; color: #999;">No enforcers found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        fetch(window.location.href, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the new HTML
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            
            // Replace only the enforcers table
            const oldTable = document.querySelector('table');
            const newTable = newDoc.querySelector('table');
            if (oldTable && newTable) {
                oldTable.innerHTML = newTable.innerHTML;
            }
        })
        .catch(error => console.error('Error refreshing dashboard:', error));
    }, 30000); // 30 seconds

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('violationsChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Number of Violations',
                        data: @json($chartData),
                        backgroundColor: '#4CAF50',
                        borderColor: '#4CAF50',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Violations'
                            },
                            ticks: {
                                stepSize: 2,
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush

<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .online-status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        background-color: rgba(40, 167, 69, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .online-status-badge.offline {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>
@endsection