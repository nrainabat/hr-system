@extends('layouts.app')

@section('content')
<style>
    :root { --corp-navy: #0f172a; --corp-blue: #1e293b; --corp-gold: #c29d59; --corp-light: #f8fafc; --corp-border: #e2e8f0; }
    .corp-heading { font-family: 'Arial', sans-serif; color: var(--corp-navy); border-bottom: 2px solid var(--corp-navy); padding-bottom: 10px; margin-bottom: 25px; }
    .card-corp { border: 1px solid var(--corp-border); border-radius: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background: white; height: 100%; }
    .card-corp-header { background-color: #fff; border-bottom: 2px solid var(--corp-border); padding: 15px 20px; font-weight: 700; color: var(--corp-navy); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .card-corp-body { padding: 20px; }
    .kpi-value { font-size: 2.2rem; font-weight: 700; color: var(--corp-navy); }
    .kpi-sub { font-size: 0.85rem; color: #64748b; }
</style>

<div class="container-fluid px-4 py-4" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="corp-heading mb-0 border-0 pb-0">Executive Dashboard</h2>
            <p class="text-muted small">Organization-wide performance metrics and workforce analytics.</p>
        </div>
        <div class="text-end">
            <span class="badge bg-dark rounded-0 p-2">{{ date('l, d F Y') }}</span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card-corp border-top-primary">
                <div class="card-corp-body">
                    <p class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem;">Total Workforce</p>
                    <div class="d-flex align-items-baseline">
                        <span class="kpi-value me-2">{{ $totalEmployees }}</span>
                        <span class="text-success small"><i class="fas fa-arrow-up"></i> Active</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-corp">
                <div class="card-corp-body">
                    <p class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem;">Daily Attendance</p>
                    <div class="d-flex align-items-baseline">
                        <span class="kpi-value me-2">{{ $attendanceRate }}%</span>
                        <span class="text-muted small">Rate</span>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $attendanceRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-corp">
                <div class="card-corp-body">
                    <p class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem;">Late Arrivals (Today)</p>
                    <div class="d-flex align-items-baseline">
                        <span class="kpi-value me-2 text-warning">{{ $lateCount }}</span>
                        <span class="text-muted small">Employees</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-corp">
                <div class="card-corp-body">
                    <p class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem;">Overtime (Month)</p>
                    <div class="d-flex align-items-baseline">
                        <span class="kpi-value me-2">{{ number_format($totalOvertimeHours) }}</span>
                        <span class="text-muted small">Hours</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card-corp">
                <div class="card-corp-header d-flex justify-content-between">
                    <span>Attendance Trend (Last 7 Days)</span>
                    <i class="fas fa-chart-line text-muted"></i>
                </div>
                <div class="card-corp-body">
                    <canvas id="trendChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-corp">
                <div class="card-corp-header">
                    <span>Department Breakdown</span>
                </div>
                <div class="card-corp-body">
                    <canvas id="deptChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card-corp">
                <div class="card-corp-header border-bottom-0 bg-white">
                    <span class="text-primary">Leave Utilization by Type (Approved)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light text-uppercase small text-muted">
                            <tr>
                                <th class="ps-4">Leave Type</th>
                                <th class="text-end pe-4">Total Taken</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveStats as $type => $count)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $type }}</td>
                                <td class="text-end pe-4">{{ $count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card-corp">
                <div class="card-corp-header border-bottom-0 bg-white">
                    <span class="text-primary">Gender Diversity</span>
                </div>
                <div class="card-corp-body d-flex justify-content-center align-items-center">
                    <canvas id="genderChart" style="max-height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const navy = '#0f172a';
    const gold = '#c29d59';
    const grey = '#cbd5e1';

    // 1. Trend Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dates) !!},
            datasets: [{
                label: 'Present Employees',
                data: {!! json_encode($attendanceTrend) !!},
                borderColor: navy,
                backgroundColor: 'rgba(15, 23, 42, 0.05)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } }
        }
    });

    // 2. Dept Chart
    new Chart(document.getElementById('deptChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($deptDist->keys()) !!},
            datasets: [{
                data: {!! json_encode($deptDist->values()) !!},
                backgroundColor: [navy, '#334155', '#475569', '#64748b', gold],
                borderWidth: 0
            }]
        },
        options: { cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
    });

    // 3. Gender Chart
    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($genderDist->keys()) !!},
            datasets: [{
                data: {!! json_encode($genderDist->values()) !!},
                backgroundColor: [navy, gold]
            }]
        }
    });
</script>
@endsection