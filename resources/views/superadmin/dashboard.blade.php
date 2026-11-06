@extends('layouts.admin')

@section('title', 'Dashboard Super Admin - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold">KONTROL TERTINGGI SISTEM</div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-shield-halved text-warning me-2"></i> Dashboard Super Admin</h2>
            <p class="text-secondary small mb-0">Pusat kendali eksekutif, manajemen hak akses staf, serta analitik menyeluruh platform AndosMusic.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.admins') }}" class="btn btn-outline-cyan btn-sm">
                <i class="fa-solid fa-user-tie me-1"></i> Kelola Admin
            </a>
            <a href="{{ route('superadmin.settings') }}" class="btn btn-cyan btn-sm">
                <i class="fa-solid fa-gear me-1"></i> Pengaturan Website
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-lg-3">
            <div class="am-card p-3">
                <div class="text-secondary small fw-bold">TOTAL REGULER USER</div>
                <div class="fs-2 fw-extrabold text-white mt-1">{{ number_format($totalUsers) }}</div>
                <div class="text-muted small mt-1">Pengguna Terdaftar</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="am-card p-3">
                <div class="text-secondary small fw-bold">ADMIN / MODERATOR</div>
                <div class="fs-2 fw-extrabold text-cyan mt-1">{{ number_format($totalAdmins) }}</div>
                <div class="text-muted small mt-1">Staf Pengelola Konten</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="am-card p-3">
                <div class="text-secondary small fw-bold">SUPER ADMIN</div>
                <div class="fs-2 fw-extrabold text-warning mt-1">{{ number_format($totalSuperAdmins) }}</div>
                <div class="text-muted small mt-1">Pemegang Hak Penuh</div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="am-card p-3">
                <div class="text-secondary small fw-bold">TOTAL PEMUTARAN</div>
                <div class="fs-2 fw-extrabold text-cyan mt-1">{{ number_format($totalPlays) }}</div>
                <div class="text-muted small mt-1">Stream Seluruh Lagu</div>
            </div>
        </div>
    </div>

    <!-- Secondary Row of Metrics -->
    <div class="row g-3 mb-5">
        <div class="col-md-4">
            <div class="am-card p-3 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">MUSIK APPROVED (PUBLIK)</div>
                        <div class="fs-4 fw-bold text-white mt-1">{{ $approvedMusic }} Lagu</div>
                    </div>
                    <i class="fa-solid fa-circle-check fs-2 text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="am-card p-3 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">UPLOAD PENDING</div>
                        <div class="fs-4 fw-bold text-white mt-1">{{ $pendingMusic }} Lagu</div>
                    </div>
                    <a href="{{ route('admin.music.pending') }}" class="btn btn-warning btn-sm fw-bold">Periksa</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="am-card p-3 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">MUSIK REJECTED</div>
                        <div class="fs-4 fw-bold text-white mt-1">{{ $rejectedMusic }} Lagu</div>
                    </div>
                    <i class="fa-solid fa-circle-xmark fs-2 text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
        <!-- Daily User Registrations Chart -->
        <div class="col-lg-6">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-user-plus text-cyan me-2"></i> Pertumbuhan Pendaftaran Pengguna (7 Hari Terakhir)</h5>
                <div style="height: 260px; position: relative;">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Plays Chart -->
        <div class="col-lg-6">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-headphones text-cyan me-2"></i> Aktivitas Pemutaran Harian (7 Hari Terakhir)</h5>
                <div style="height: 260px; position: relative;">
                    <canvas id="dailyPlaysChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Top Music & Recent Logs -->
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-fire text-danger me-2"></i> Musik Terpopuler</h5>
                <div class="table-responsive">
                    <table class="table-andos mb-0">
                        <thead>
                            <tr>
                                <th>Lagu</th>
                                <th>Genre</th>
                                <th class="text-end">Putaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topMusic as $music)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 36px; height: 36px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-white small">{{ $music->title }}</div>
                                                <div class="text-secondary" style="font-size: 0.72rem;">{{ $music->artist }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="genre-tag">{{ $music->genre }}</span></td>
                                    <td class="text-end fw-bold text-cyan">{{ number_format($music->plays_count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="am-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-white mb-0"><i class="fa-solid fa-list-check text-cyan me-2"></i> Log Aktivitas Sistem</h5>
                    <a href="{{ route('superadmin.activity-logs') }}" class="btn btn-outline-cyan btn-sm">Lihat Semua Log</a>
                </div>

                <div class="activity-timeline">
                    @foreach($recentActivities as $log)
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div class="brand-logo-icon rounded-circle flex-shrink-0" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $log->activity }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-white small text-truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. User Growth Chart
    const userGrowthData = {!! json_encode($userGrowth) !!};
    const userDates = Object.keys(userGrowthData);
    const userCounts = Object.values(userGrowthData);

    const ctxUser = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(ctxUser, {
        type: 'bar',
        data: {
            labels: userDates,
            datasets: [{
                label: 'Pengguna Baru',
                data: userCounts,
                backgroundColor: '#00D9E8',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#A0B4BA' } },
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#A0B4BA', precision: 0 } }
            }
        }
    });

    // 2. Daily Plays Chart
    const dailyPlaysData = {!! json_encode($dailyPlays) !!};
    const playDates = Object.keys(dailyPlaysData);
    const playCounts = Object.values(dailyPlaysData);

    const ctxPlays = document.getElementById('dailyPlaysChart').getContext('2d');
    new Chart(ctxPlays, {
        type: 'line',
        data: {
            labels: playDates,
            datasets: [{
                label: 'Pemutaran',
                data: playCounts,
                borderColor: '#00AFC1',
                backgroundColor: 'rgba(0, 175, 193, 0.15)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#00AFC1',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#A0B4BA' } },
                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#A0B4BA', precision: 0 } }
            }
        }
    });
</script>
@endpush

