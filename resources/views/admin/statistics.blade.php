@extends('layouts.admin')

@section('title', 'Statistik & Analitik - AndosMusic')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1"><i class="fa-solid fa-chart-line text-cyan me-2"></i> Statistik & Analisis Platform</h2>
            <p class="text-secondary small mb-0">Visualisasi data pemutaran musik dan sebaran genre lagu di AndosMusic.</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-5">
        <!-- Monthly Streaming Plays Chart -->
        <div class="col-lg-7">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-chart-simple text-cyan me-2"></i> Tren Pemutaran Musik (6 Bulan Terakhir)</h5>
                <div style="height: 280px; position: relative;">
                    <canvas id="monthlyPlaysChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Genre Distribution Chart -->
        <div class="col-lg-5">
            <div class="am-card p-4 h-100">
                <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-chart-pie text-cyan me-2"></i> Distribusi Genre Musik</h5>
                <div style="height: 280px; position: relative;">
                    <canvas id="genreChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Most Streamed Tracks -->
    <div class="am-card p-4 mb-5">
        <h5 class="fw-bold text-white mb-4"><i class="fa-solid fa-fire text-danger me-2"></i> 10 Lagu Paling Banyak Diputar Sepanjang Waktu</h5>

        <div class="table-responsive">
            <table class="table-andos mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">Rank</th>
                        <th>Lagu & Artis</th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-end">Total Pemutaran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topTracks as $index => $music)
                        <tr>
                            <td class="text-center fw-bold">
                                @if($index === 0)
                                    <span class="badge rounded-circle p-2" style="background: #F59E0B; color: #000; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                @elseif($index === 1)
                                    <span class="badge rounded-circle p-2" style="background: #94A3B8; color: #000; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                @elseif($index === 2)
                                    <span class="badge rounded-circle p-2" style="background: #00D9E8; color: #000; width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                @else
                                    <span class="text-muted">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $music->cover_url }}" alt="{{ $music->title }}" class="rounded-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-white small">{{ $music->title }}</div>
                                        <div class="text-secondary small">{{ $music->artist }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-secondary small">{{ $music->album ?: '-' }}</td>
                            <td><span class="genre-tag">{{ $music->genre }}</span></td>
                            <td class="text-center text-muted small">{{ $music->formatted_duration }}</td>
                            <td class="text-end fw-bold text-cyan">{{ number_format($music->plays_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Monthly Plays Chart
    const monthlyData = {!! json_encode($monthlyPlays) !!};
    const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    const playLabels = Object.keys(monthlyData).map(m => monthNames[parseInt(m)] || `Bulan ${m}`);
    const playValues = Object.values(monthlyData);

    const ctxPlays = document.getElementById('monthlyPlaysChart').getContext('2d');
    new Chart(ctxPlays, {
        type: 'line',
        data: {
            labels: playLabels.length ? playLabels : ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt'],
            datasets: [{
                label: 'Jumlah Pemutaran',
                data: playValues.length ? playValues : [120, 190, 300, 500, 850, 1200],
                borderColor: '#00D9E8',
                backgroundColor: 'rgba(0, 217, 232, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#00D9E8',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#A0B4BA' }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#A0B4BA', precision: 0 }
                }
            }
        }
    });

    // 2. Genre Distribution Chart
    const genreData = {!! json_encode($genreCounts) !!};
    const genreLabels = Object.keys(genreData);
    const genreValues = Object.values(genreData);

    const ctxGenre = document.getElementById('genreChart').getContext('2d');
    new Chart(ctxGenre, {
        type: 'doughnut',
        data: {
            labels: genreLabels.length ? genreLabels : ['Electronic', 'Pop', 'EDM', 'Acoustic', 'Jazz'],
            datasets: [{
                data: genreValues.length ? genreValues : [30, 25, 20, 15, 10],
                backgroundColor: ['#00D9E8', '#00AFC1', '#26C6DA', '#4DD0E1', '#80DEEA', '#B2EBF2'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#A0B4BA', boxWidth: 12, padding: 15 }
                }
            }
        }
    });
</script>
@endpush

