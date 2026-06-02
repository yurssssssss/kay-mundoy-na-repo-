@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('styles')
<style>
    .chart-card {
        background: #fff;
        border-radius: 14px;
        padding: 1.4rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        min-width: 0;
        overflow: hidden;
    }
    .chart-card .chart-title {
        font-weight: 700;
        color: #1a3c6e;
        font-size: .95rem;
        margin-bottom: 1rem;
    }
    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 260px;
        overflow: hidden;
    }
    .chart-wrapper canvas {
        display: block;
        max-width: 100% !important;
        max-height: 260px !important;
    }
</style>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon-box bg-primary bg-opacity-10">
                <i class="bi bi-people-fill text-primary"></i>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon-box bg-success bg-opacity-10">
                <i class="bi bi-book-fill text-success"></i>
            </div>
            <div>
                <div class="stat-value">{{ number_format($totalSubjects) }}</div>
                <div class="stat-label">Total Subjects</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon-box bg-warning bg-opacity-10">
                <i class="bi bi-bookmark-fill text-warning"></i>
            </div>
            <div>
                <div class="stat-value">{{ Auth::user()->subjects()->count() }}</div>
                <div class="stat-label">My Subjects</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="icon-box bg-info bg-opacity-10">
                <i class="bi bi-calendar3 text-info"></i>
            </div>
            <div>
                <div class="stat-value">{{ now()->format('M Y') }}</div>
                <div class="stat-label">Current Period</div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Subjects by Semester</div>
            <div class="chart-wrapper">
                <canvas id="semesterChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Subjects by Units</div>
            <div class="chart-wrapper">
                <canvas id="unitsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-graph-up me-2 text-warning"></i>Subjects Added (Last 6 Months)</div>
            <div class="chart-wrapper">
                <canvas id="subjectsLineChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <div class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-info"></i>Users Registered (Last 6 Months)</div>
            <div class="chart-wrapper">
                <canvas id="usersLineChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// Wait for full page render before initialising charts
window.addEventListener('load', function () {
    setTimeout(function () {

        const palette = ['#2756a8','#f4a623','#22c55e','#ef4444','#8b5cf6','#06b6d4'];
        const CHART_H = 260;

        function getW(id) {
            return document.getElementById(id).parentElement.clientWidth || 500;
        }

        // ── Semester Doughnut ──────────────────────────────────
        const semesterData = @json($subjectBySemester);
        const semCanvas = document.getElementById('semesterChart');
        semCanvas.width  = getW('semesterChart');
        semCanvas.height = CHART_H;
        new Chart(semCanvas, {
            type: 'doughnut',
            data: {
                labels: Object.keys(semesterData).length ? Object.keys(semesterData) : ['No Data'],
                datasets: [{
                    data: Object.keys(semesterData).length ? Object.values(semesterData) : [1],
                    backgroundColor: palette,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
                }
            }
        });

        // ── Units Bar ──────────────────────────────────────────
        const unitsData = @json($subjectByUnits);
        const unitsCanvas = document.getElementById('unitsChart');
        unitsCanvas.width  = getW('unitsChart');
        unitsCanvas.height = CHART_H;
        new Chart(unitsCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(unitsData).map(u => u + ' Unit' + (u > 1 ? 's' : '')),
                datasets: [{
                    label: 'Subjects',
                    data: Object.values(unitsData),
                    backgroundColor: '#2756a8',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });

        // ── Subjects Line ──────────────────────────────────────
        const subjectsMonths = @json($subjectsPerMonth);
        const subjectsCanvas = document.getElementById('subjectsLineChart');
        subjectsCanvas.width  = getW('subjectsLineChart');
        subjectsCanvas.height = CHART_H;
        new Chart(subjectsCanvas, {
            type: 'line',
            data: {
                labels: Object.keys(subjectsMonths).length ? Object.keys(subjectsMonths) : ['No Data'],
                datasets: [{
                    label: 'Subjects Added',
                    data: Object.values(subjectsMonths),
                    borderColor: '#f4a623',
                    backgroundColor: 'rgba(244,166,35,.12)',
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#f4a623',
                    fill: true,
                    tension: .35
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // ── Users Line ─────────────────────────────────────────
        const usersMonths = @json($usersPerMonth);
        const usersCanvas = document.getElementById('usersLineChart');
        usersCanvas.width  = getW('usersLineChart');
        usersCanvas.height = CHART_H;
        new Chart(usersCanvas, {
            type: 'line',
            data: {
                labels: Object.keys(usersMonths).length ? Object.keys(usersMonths) : ['No Data'],
                datasets: [{
                    label: 'Users Registered',
                    data: Object.values(usersMonths),
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,.12)',
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#22c55e',
                    fill: true,
                    tension: .35
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

    }, 50); // 50ms delay ensures content-area has fully settled
});
</script>
@endsection