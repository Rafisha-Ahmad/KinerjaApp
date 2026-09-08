<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Kinerja Pegawai</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .flex-box { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { background: #f8f9fa; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; flex: 1; }
        .chart-container { position: relative; height: 350px; margin: 20px 0; }
        .pegawai-card { background: #ebf8ff; border-left: 4px solid #3182ce; padding: 12px; margin-bottom: 8px; border-radius: 4px; }
        .log-item { padding: 8px; border-bottom: 1px solid #edf2f7; font-size: 0.9em; }
        button { background: #3182ce; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 Dashboard Kinerja Pegawai</h2>

    @if(session('success'))
        <div style="background: #c6f6d5; color: #22543d; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex-box">
        <div class="card">
            <h4>📁 Upload / Revisi Laporan</h4>
            <form action="{{ route('pekerjaan.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label>Tanggal Laporan:</label><br>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required><br><br>
                <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required><br><br>
                <button type="submit">Unggah Laporan</button>
            </form>
        </div>

        <div class="card">
            <h4>🔍 Filter Tanggal Laporan</h4>
            <form action="{{ route('dashboard') }}" method="GET">
                <label>Pilih Tanggal:</label><br>
                <input type="date" name="tanggal" value="{{ $tanggalPilihan }}"><br><br>
                <button type="submit">Tampilkan</button>
            </form>
        </div>
    </div>

    <hr>

    <h3>Laporan Tanggal: {{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('d F Y') }}</h3>

    <!-- Diagram Batang Horizontal -->
    <div class="chart-container">
        <canvas id="kinerjaChart"></canvas>
    </div>

    <!-- Detail Pekerjaan -->
    <div>
        <h4>📋 Detail Pekerjaan</h4>
        @forelse ($pekerjaans as $p)
            <div class="pegawai-card">
                <b>{{ $p->user->name }}</b>: {{ $p->daftar_pekerjaan }} (Total: {{ $p->total_pekerjaan }})
            </div>
        @empty
            <p style="color: #a0aec0;">Belum ada data di tanggal ini.</p>
        @endforelse
    </div>

    <hr style="margin-top: 30px;">

    <!-- Log Upload -->
    <div>
        <h4>📜 Log Riwayat Upload</h4>
        @foreach($logRiwayat as $log)
            <div class="log-item">
                📅 <b>{{ $log->tanggal }}</b> — File: <code>{{ $log->file_name }}</code>
                <a href="{{ route('dashboard', ['tanggal' => $log->tanggal]) }}" style="margin-left: 10px; color: #3182ce;">[Lihat Grafik]</a>
            </div>
        @endforeach
    </div>
</div>

<script>
    const labels = @json($labels);
    const totals = @json($totals);

    const ctx = document.getElementById('kinerjaChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Pekerjaan Selesai',
                data: totals,
                backgroundColor: 'rgba(49, 130, 206, 0.7)',
                borderColor: 'rgba(49, 130, 206, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });
</script>

</body>
</html>