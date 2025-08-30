{{-- resources/views/reports/pdf.blade.php --}}
<!DOCreport_type html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Status Gizi Balita</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        .stats { display: flex; justify-content: space-between; margin: 20px 0; }
        .stat-box { text-align: center; border: 1px solid #ddd; padding: 10px; width: 22%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .status-stunting { background-color: #fee2e2; color: #dc2626; }
        .status-berisiko { background-color: #fef3c7; color: #d97706; }
        .status-normal { background-color: #dcfce7; color: #16a34a; }
        .status-lebih { background-color: #dbeafe; color: #2563eb; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN STATUS GIZI BALITA</h1>
        <p>{{ strtoupper($reportData['report_type']) }}</p>
        <p>Periode: {{ $reportData['period_start']->format('d F Y') }} - {{ $reportData['period_end']->format('d F Y') }}</p>
        <p>Dibuat: {{ $reportData['generated_at']->format('d F Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>{{ $reportData['stats']['total_balita'] }}</h3>
            <p>Total Balita</p>
        </div>
        <div class="stat-box">
            <h3 style="color: #dc2626;">{{ $reportData['stats']['stunting'] }}</h3>
            <p>Stunting ({{ $reportData['stats']['stunting_pct'] }}%)</p>
        </div>
        <div class="stat-box">
            <h3 style="color: #d97706;">{{ $reportData['stats']['berisiko_stunting'] }}</h3>
            <p>Berisiko ({{ $reportData['stats']['berisiko_pct'] }}%)</p>
        </div>
        <div class="stat-box">
            <h3 style="color: #16a34a;">{{ $reportData['stats']['normal'] }}</h3>
            <p>Normal</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Balita</th>
                <th>L/P</th>
                <th>Umur (bulan)</th>
                <th>Tanggal Ukur</th>
                <th>BB (kg)</th>
                <th>TB (cm)</th>
                <th>Status Gizi</th>
                <th>Confidence</th>
                <th>Posyandu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['predictions'] as $index => $prediction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $prediction->pengukuran->balita->nama_balita }}</td>
                <td>{{ $prediction->pengukuran->balita->jenis_kelamin }}</td>
                <td>{{ $prediction->pengukuran->umur_bulan }}</td>
                <td>{{ $prediction->pengukuran->tanggal_pengukuran->format('d/m/Y') }}</td>
                <td>{{ $prediction->pengukuran->berat_badan }}</td>
                <td>{{ $prediction->pengukuran->tinggi_badan }}</td>
                <td class="status-{{ str_replace('_', '-', $prediction->prediksi_status) }}">
                    {{ ucwords(str_replace('_', ' ', $prediction->prediksi_status)) }}
                </td>
                <td>{{ number_format($prediction->confidence_level, 1) }}%</td>
                <td>{{ $prediction->pengukuran->balita->posyandu }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat menggunakan Sistem Prediksi Status Gizi Balita berbasis Fuzzy-AHP</p>
        <p>© {{ date('Y') }} Sistem Prediksi Gizi Balita. Dicetak pada {{ now()->format('d F Y H:i') }}</p>
    </div>
</body>
</html>