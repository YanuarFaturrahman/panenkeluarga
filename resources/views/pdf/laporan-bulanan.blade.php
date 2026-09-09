<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan PanenKeluarga</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2 { margin-bottom: 2px; color: #214332; }
        p { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #214332; font-weight: bold; }
        .summary-box { background: #f9f9f9; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>Laporan Performa PanenKeluarga</h2>
    <p>Periode: Bulan {{ $bulan }} / Tahun {{ $tahun }}</p>

    <div class="summary-box">
        <strong>Ringkasan:</strong><br>
        Total Transaksi: {{ number_format($totalTransaksi) }}<br>
        Total GMV: Rp{{ number_format($totalGmv, 0, ',', '.') }}<br>
        Surplus Subsidi: Rp{{ number_format($surplusSubsidi, 0, ',', '.') }}
    </div>

    <h3>Rincian Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Jumlah Bayar</th>
                <th>Alokasi Subsidi</th>
                <th>Metode Bayar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->kode_transaksi ?? '-' }}</td>
                    <td>Rp{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->alokasi_subsidi, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($item->metode_bayar ?? '-') }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada transaksi di periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>