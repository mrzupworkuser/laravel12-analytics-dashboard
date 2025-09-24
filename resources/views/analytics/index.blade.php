@extends('layouts.app')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
    <div class="grid">
        <x-kpi-card label="Users" :value="number_format($metrics['users'])" subtitle="Updated now — Manohar Zarkar" style="grid-column: span 3" />
        <x-kpi-card label="Revenue" :value="'$'.number_format($metrics['revenue'], 2)" :subtitle="'Growth '.$metrics['growth'].'% — Manohar Zarkar'" style="grid-column: span 3" />
        <x-kpi-card label="Orders" :value="number_format($metrics['orders'])" subtitle="Monthly — Manohar Zarkar" style="grid-column: span 3" />
        <x-kpi-card label="Growth" :value="$metrics['growth'].'%'" subtitle="vs previous — Manohar Zarkar" style="grid-column: span 3" />

        <div class="card chart-card" style="grid-column: span 12">
            <canvas id="chart-revenue" height="80"></canvas>
        </div>
        <div class="card chart-card" style="grid-column: span 6">
            <canvas id="chart-orders" height="80"></canvas>
        </div>
        <div class="card chart-card" style="grid-column: span 6">
            <canvas id="chart-users" height="80"></canvas>
        </div>
        <div class="card chart-card" style="grid-column: span 12">
            <canvas id="chart-growth" height="60"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const initialSeries = @json($series);

    let revenueChart, ordersChart, usersChart, growthChart;

    function makeGradient(ctx, color){
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, color);
        gradient.addColorStop(1, 'rgba(255,255,255,0.02)');
        return gradient;
    }

    function buildCharts(series){
        const { labels, datasets } = series;

        const revenueCtx = document.getElementById('chart-revenue').getContext('2d');
        const ordersCtx = document.getElementById('chart-orders').getContext('2d');
        const usersCtx = document.getElementById('chart-users').getContext('2d');
        const growthCtx = document.getElementById('chart-growth').getContext('2d');

        const gridColor = 'rgba(255,255,255,0.08)';
        const tickColor = 'rgba(255,255,255,0.6)';

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor } },
                y: { grid: { color: gridColor }, ticks: { color: tickColor } }
            },
            plugins: { legend: { labels: { color: tickColor } } }
        };

        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue',
                    data: datasets.revenue,
                    borderColor: '#22d3ee',
                    backgroundColor: makeGradient(revenueCtx, 'rgba(34,211,238,0.28)'),
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: commonOptions
        });

        ordersChart = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data: datasets.orders,
                    backgroundColor: '#34d399'
                }]
            },
            options: commonOptions
        });

        usersChart = new Chart(usersCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Users',
                    data: datasets.users,
                    borderColor: '#93c5fd',
                    backgroundColor: makeGradient(usersCtx, 'rgba(147,197,253,0.25)'),
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: commonOptions
        });

        growthChart = new Chart(growthCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Growth %',
                    data: datasets.growth,
                    borderColor: '#f59e0b',
                    backgroundColor: makeGradient(growthCtx, 'rgba(245,158,11,0.25)'),
                    tension: 0.35,
                    fill: true,
                    pointRadius: 0
                }]
            },
            options: commonOptions
        });
    }

    function updateKpis(metrics){
        document.getElementById('kpi-users').textContent = new Intl.NumberFormat().format(metrics.users);
        document.getElementById('kpi-revenue').textContent = `$${metrics.revenue.toLocaleString(undefined,{ minimumFractionDigits:2, maximumFractionDigits:2 })}`;
        document.getElementById('kpi-orders').textContent = new Intl.NumberFormat().format(metrics.orders);
        document.getElementById('kpi-growth').textContent = `${metrics.growth}%`;
        document.getElementById('kpi-revenue-delta').textContent = `Growth ${metrics.growth}%`;
    }

    function refresh(days = 14){
        fetch(`{{ route('analytics.data') }}?days=${days}`)
            .then(r => r.json())
            .then(({ metrics, series }) => {
                updateKpis(metrics);

                const { labels, datasets } = series;

                revenueChart.data.labels = labels;
                revenueChart.data.datasets[0].data = datasets.revenue;
                revenueChart.update();

                ordersChart.data.labels = labels;
                ordersChart.data.datasets[0].data = datasets.orders;
                ordersChart.update();

                usersChart.data.labels = labels;
                usersChart.data.datasets[0].data = datasets.users;
                usersChart.update();

                growthChart.data.labels = labels;
                growthChart.data.datasets[0].data = datasets.growth;
                growthChart.update();
            });
    }

    // Initialize
    buildCharts(initialSeries);
    // Optionally auto-refresh every 60s
    setInterval(() => refresh(14), 60000);
</script>
@endsection


