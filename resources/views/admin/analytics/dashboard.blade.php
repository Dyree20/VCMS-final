@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="analytics-container">
    <!-- Header with Period Selector -->
    <div class="analytics-header">
        <div class="analytics-header-title">
            <h1 class="analytics-title">
                <i class="fa-solid fa-chart-line"></i>
                Analytics Dashboard
            </h1>
            <p class="analytics-subtitle">System Performance & Statistics</p>
        </div>
        <div class="analytics-period-selector">
            <button class="period-btn active" data-period="today">Today</button>
            <button class="period-btn" data-period="weekly">Weekly</button>
            <button class="period-btn" data-period="monthly">Monthly</button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="analytics-metrics-grid">
        <!-- Clampings Card -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Clampings</h3>
                <i class="fa-solid fa-car-side"></i>
            </div>
            <div class="analytics-metric-value today-value">{{ $clampingsToday }}</div>
            <div class="analytics-metric-value weekly-value" style="display: none;">{{ $clampingsWeek }}</div>
            <div class="analytics-metric-value monthly-value" style="display: none;">{{ $clampingsMonth }}</div>
            <div class="analytics-metric-subtitle">
                <span class="today-subtitle">Month: {{ $clampingsMonth }} | Year: {{ $clampingsYear }}</span>
                <span class="weekly-subtitle" style="display: none;">This Week</span>
                <span class="monthly-subtitle" style="display: none;">This Month</span>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Revenue</h3>
                <i class="fa-solid fa-peso-sign"></i>
            </div>
            <div class="analytics-metric-value today-value">₱{{ number_format($revenueToday, 2) }}</div>
            <div class="analytics-metric-value weekly-value" style="display: none;">₱{{ number_format($revenueWeek, 2) }}</div>
            <div class="analytics-metric-value monthly-value" style="display: none;">₱{{ number_format($revenueMonth, 2) }}</div>
            <div class="analytics-metric-subtitle">
                <span class="today-subtitle">Month: ₱{{ number_format($revenueMonth, 2) }}</span>
                <span class="weekly-subtitle" style="display: none;">Year: ₱{{ number_format($revenueYear, 2) }}</span>
                <span class="monthly-subtitle" style="display: none;">Year: ₱{{ number_format($revenueYear, 2) }}</span>
            </div>
        </div>

        <!-- Released Today Card -->
        <div class="analytics-metric-card">
            <div class="analytics-metric-header">
                <h3>Released</h3>
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div class="analytics-metric-value today-value">{{ $releasedToday }}</div>
            <div class="analytics-metric-value weekly-value" style="display: none;">{{ $releasedWeek }}</div>
            <div class="analytics-metric-value monthly-value" style="display: none;">{{ $releasedMonth }}</div>
            <div class="analytics-metric-subtitle">
                <span class="today-subtitle">This Month: {{ $releasedMonth }}</span>
                <span class="weekly-subtitle" style="display: none;">This Week</span>
                <span class="monthly-subtitle" style="display: none;">This Month</span>
            </div>
        </div>

        <!-- Yearly Revenue Card -->
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

        <!-- Trend Chart -->
        <div class="analytics-chart-card">
            <h3 class="analytics-chart-title" id="trendTitle">Daily Trend (This Month)</h3>
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


</div>

<link rel="stylesheet" href="{{ asset('styles/analytics.css') }}">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let trendChart = null;

    // Data
    const statusData = {!! json_encode($statusBreakdown) !!};
    const dailyData = {!! json_encode($dailyData) !!};
    const weeklyData = {!! json_encode($weeklyData) !!};
    const monthlyData = {!! json_encode($monthlyData) !!};

    // Status Breakdown Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
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

    // Trend Chart - Initial (Daily)
    function initTrendChart(data, title, yAxisLabel) {
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        
        if (trendChart) {
            trendChart.destroy();
        }

        // Prepare chart labels
        let labels = [];
        if (title.includes('Daily')) {
            labels = data.map(d => d.date);
        } else if (title.includes('Weekly')) {
            labels = data.map((d, i) => `Week ${d.week}`);
        } else if (title.includes('Monthly')) {
            labels = data.map(d => d.month);
        }

        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Clampings',
                    data: data.map(d => d.count),
                    borderColor: '#2b58ff',
                    backgroundColor: 'rgba(43, 88, 255, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#2b58ff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }, {
                    label: 'Revenue (₱)',
                    data: data.map(d => d.revenue / 1000),
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

        // Update title
        document.getElementById('trendTitle').textContent = title;
    }

    // Initialize with daily data
    initTrendChart(dailyData, 'Daily Trend (This Month)', 'Clampings Count');

    // Period selector buttons
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const period = this.dataset.period;

            // Update metric card values
            document.querySelectorAll('.today-value, .today-subtitle').forEach(el => el.style.display = period === 'today' ? 'block' : 'none');
            document.querySelectorAll('.weekly-value, .weekly-subtitle').forEach(el => el.style.display = period === 'weekly' ? 'block' : 'none');
            document.querySelectorAll('.monthly-value, .monthly-subtitle').forEach(el => el.style.display = period === 'monthly' ? 'block' : 'none');

            // Update trend chart
            switch(period) {
                case 'today':
                    initTrendChart(dailyData, 'Daily Trend (This Month)', 'Clampings Count');
                    break;
                case 'weekly':
                    initTrendChart(weeklyData, 'Weekly Trend (This Year)', 'Clampings Count');
                    break;
                case 'monthly':
                    initTrendChart(monthlyData, 'Monthly Trend (Last 12 Months)', 'Clampings Count');
                    break;
            }
        });
    });
</script>

@endsection
