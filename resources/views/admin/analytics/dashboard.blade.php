@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="analytics-container">
    <!-- Header -->
    <div class="analytics-header">
        <h1 class="analytics-title">
            <i class="fa-solid fa-chart-line"></i>
            Analytics Dashboard
        </h1>
        <p class="analytics-subtitle">System Performance & Statistics</p>
    </div>

    <!-- Key Metrics Grid -->
    <div class="analytics-metrics-grid">
        <!-- Clampings Today -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Clampings Today</h3>
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="analytics-metric-value">{{ $clampingsToday }}</div>
            <div class="analytics-metric-subtitle">Month: {{ $clampingsMonth }} | Year: {{ $clampingsYear }}</div>
        </div>

        <!-- Revenue Today -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Revenue Today</h3>
                <i class="fa-solid fa-peso-sign"></i>
            </div>
            <div class="analytics-metric-value">₱{{ number_format($revenueToday, 2) }}</div>
            <div class="analytics-metric-subtitle">Month: ₱{{ number_format($revenueMonth, 2) }}</div>
        </div>

        <!-- Appeals Pending -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Appeals Pending</h3>
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div class="analytics-metric-value">{{ $appealStats['pending'] }}</div>
            <div class="analytics-metric-subtitle">Total: {{ $appealStats['total'] }}</div>
        </div>

        <!-- Yearly Revenue -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Yearly Revenue</h3>
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div class="analytics-metric-value">₱{{ number_format($revenueYear, 2) }}</div>
            <div class="analytics-metric-subtitle">Clampings: {{ $clampingsYear }}</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="analytics-charts-grid">
        <!-- Status Breakdown -->
        <div class="analytics-chart-card">
            <h3 class="analytics-chart-title">Clamping Status Breakdown (This Month)</h3>
            <div class="analytics-chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="analytics-chart-card">
            <h3 class="analytics-chart-title">Monthly Trend</h3>
            <div class="analytics-chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Enforcers -->
    <div class="analytics-section">
        <h2 class="analytics-section-title">Top Enforcers (This Month)</h2>
        <div class="analytics-table-wrapper">
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Enforcer Name</th>
                        <th>Clampings</th>
                        <th>Total Fines</th>
                        <th>Average Fine</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topEnforcers as $index => $enforcer)
                    <tr>
                        <td class="rank-badge">{{ $index + 1 }}</td>
                        <td class="enforcer-name">{{ $enforcer->user->f_name }} {{ $enforcer->user->l_name }}</td>
                        <td class="metric-number">{{ $enforcer->clampings_count }}</td>
                        <td class="metric-money">₱{{ number_format($enforcer->total_fines, 2) }}</td>
                        <td class="metric-money">₱{{ number_format($enforcer->total_fines / $enforcer->clampings_count, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Appeal Statistics -->
    <div class="analytics-section">
        <h2 class="analytics-section-title">Appeal Statistics</h2>
        <div class="analytics-appeal-stats">
            <div class="appeal-stat">
                <div class="appeal-stat-value">{{ $appealStats['total'] }}</div>
                <div class="appeal-stat-label">Total Appeals</div>
            </div>
            <div class="appeal-stat pending">
                <div class="appeal-stat-value">{{ $appealStats['pending'] }}</div>
                <div class="appeal-stat-label">Pending</div>
            </div>
            <div class="appeal-stat review">
                <div class="appeal-stat-value">{{ $appealStats['under_review'] }}</div>
                <div class="appeal-stat-label">Under Review</div>
            </div>
            <div class="appeal-stat approved">
                <div class="appeal-stat-value">{{ $appealStats['approved'] }}</div>
                <div class="appeal-stat-label">Approved</div>
            </div>
            <div class="appeal-stat rejected">
                <div class="appeal-stat-value">{{ $appealStats['rejected'] }}</div>
                <div class="appeal-stat-label">Rejected</div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('styles/analytics.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Breakdown Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = {!! json_encode($statusBreakdown) !!};
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#2b58ff',
                    '#16a34a',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6'
                ],
                borderColor: '#ffffff',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Monthly Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const monthlyData = {!! json_encode($monthlyData) !!};
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => d.date),
            datasets: [{
                label: 'Clampings',
                data: monthlyData.map(d => d.count),
                borderColor: '#2b58ff',
                backgroundColor: 'rgba(43, 88, 255, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2b58ff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }, {
                label: 'Revenue (₱)',
                data: monthlyData.map(d => d.revenue / 1000),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#16a34a',
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Clampings Count'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (₱1000s)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>

@endsection
