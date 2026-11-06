@extends('layouts.admin')

@section('title', 'Activity Logs - AndosMusic Super Admin')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-clock-rotate-left text-cyan me-2"></i> Activity Logs Sistem</h2>
            <p class="text-secondary small mb-0">Audit jejak aktivitas administratif, autentikasi, serta modifikasi data penting.</p>
        </div>
        <div class="text-secondary small">
            Total Log: <strong class="text-cyan">{{ $logs->total() }}</strong> entri
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="am-card p-3 mb-4">
        <form action="{{ route('superadmin.activity-logs') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari deskripsi atau IP address...">
            </div>
            <div class="col-md-4">
                <select name="activity" class="form-select">
                    <option value="">Semua Jenis Aktivitas</option>
                    @foreach($activities as $act)
                        <option value="{{ $act }}" {{ request('activity') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-cyan flex-grow-1">Filter</button>
                @if(request()->hasAny(['q', 'activity']))
                    <a href="{{ route('superadmin.activity-logs') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
                @endif
            </div>
        </form>
    </div>

    <div class="am-card p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>User Pelaku</th>
                        <th>Aktivitas</th>
                        <th>Deskripsi Tindakan</th>
                        <th>IP Address</th>
                        <th>Waktu Kejadian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                        <tr>
                            <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                @if($log->user)
                                    <div class="fw-bold text-white small">{{ $log->user->name }}</div>
                                    <div class="text-secondary" style="font-size: 0.72rem;">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-muted small">Sistem / Tamu</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary" style="font-size: 0.72rem;">{{ $log->activity }}</span></td>
                            <td class="text-white small" style="max-width: 350px;">
                                <div class="text-break">{{ $log->description }}</div>
                            </td>
                            <td class="text-muted small"><code>{{ $log->ip_address ?: '127.0.0.1' }}</code></td>
                            <td class="text-muted small">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat aktivitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

