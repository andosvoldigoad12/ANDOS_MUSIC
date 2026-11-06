@extends('layouts.admin')

@section('title', 'Dashboard Admin - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-gauge-high text-cyan me-2"></i> Dashboard Admin</h2>
            <p class="text-secondary small mb-0">Ringkasan aktivitas platform, moderasi konten, dan pertumbuhan AndosMusic.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.music.pending') }}" class="btn btn-warning btn-sm fw-bold">
                <i class="fa-solid fa-hourglass-half me-1"></i> {{ $pendingMusic }} Musik Menunggu Review
            </a>
            <a href="{{ route('admin.music.create') }}" class="btn btn-cyan btn-sm">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Musik
            </a>
        </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="row g-3 mb-5">
        <div class="col-sm-6 col-xl-3">
            <div class="am-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">TOTAL PENGGUNA</div>
                        <div class="fs-3 fw-bold text-white mt-1">{{ number_format($totalUsers) }}</div>
                    </div>
                    <div class="brand-logo-icon" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="am-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">TOTAL MUSIK</div>
                        <div class="fs-3 fw-bold text-white mt-1">{{ number_format($totalMusic) }}</div>
                    </div>
                    <div class="brand-logo-icon" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fa-solid fa-music"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="am-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">TOTAL PEMUTARAN</div>
                        <div class="fs-3 fw-bold text-cyan mt-1">{{ number_format($totalPlays) }}</div>
                    </div>
                    <div class="brand-logo-icon" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fa-solid fa-headphones"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="am-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-secondary small fw-semibold">STATUS MUSIK</div>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge bg-success" title="Approved">{{ $approvedMusic }} Disetujui</span>
                            <span class="badge bg-warning text-dark" title="Pending">{{ $pendingMusic }} Pending</span>
                            <span class="badge bg-danger" title="Rejected">{{ $rejectedMusic }} Ditolak</span>
                        </div>
                    </div>
                    <div class="brand-logo-icon" style="width: 44px; height: 44px; font-size: 1.2rem;">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Top Played Tracks -->
        <div class="col-lg-7">
            <div class="am-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-white mb-0">
                        <i class="fa-solid fa-fire text-danger me-2"></i> 5 Musik Terpopuler
                    </h5>
                    <a href="{{ route('admin.statistics') }}" class="btn btn-outline-cyan btn-sm">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table-andos mb-0">
                        <thead>
                            <tr>
                                <th>Lagu</th>
                                <th>Genre</th>
                                <th class="text-end">Pemutaran</th>
                                <th class="text-center" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMusic as $music)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <div class="fw-bold text-white small">{{ $music->title }}</div>
                                                <div class="text-secondary small">{{ $music->artist }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="genre-tag">{{ $music->genre }}</span></td>
                                    <td class="text-end fw-bold text-cyan">{{ number_format($music->plays_count) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('music.show', $music->id) }}" class="btn btn-link text-cyan p-0">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada statistik pemutaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent System Activities -->
        <div class="col-lg-5">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4">
                    <i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i> Aktivitas Terbaru
                </h5>

                <div class="activity-timeline">
                    @forelse($recentActivities as $log)
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div class="brand-logo-icon rounded-circle flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">{{ $log->activity }}</span>
                                    <span class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-white small text-truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small text-center py-4">Belum ada log aktivitas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

