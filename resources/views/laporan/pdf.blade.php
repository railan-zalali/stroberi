<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan {{ $bulan }} {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f8f9fa;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 10px 0;
            font-size: 16px;
            color: #666;
        }

        .summary {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .summary-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .summary-item {
            width: 30%;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #ddd;
        }

        .summary-item:nth-child(1) {
            border-left-color: #10B981;
        }

        .summary-item:nth-child(2) {
            border-left-color: #EF4444;
        }

        .summary-item:nth-child(3) {
            border-left-color: #3B82F6;
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: bold;
        }

        .value-income {
            color: #10B981;
        }

        .value-expense {
            color: #EF4444;
        }

        .value-profit {
            color: #3B82F6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        th {
            background-color: #4B5563;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-income {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-expense {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .expiry-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #FEF2F2;
            border-left: 4px solid #EF4444;
            border-radius: 8px;
            color: #991B1B;
        }
        
        .chart-container {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <p>Periode: {{ $bulan }} {{ $tahun }}</p>
    </div>

    <div class="summary">
        <div class="summary-title">Ringkasan Keuangan</div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Pemasukan</div>
                <div class="summary-value value-income">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Pengeluaran</div>
                <div class="summary-value value-expense">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Laba Bersih</div>
                <div class="summary-value value-profit">Rp {{ number_format($laba, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $transaksi->jenis == 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                            {{ ucfirst($transaksi->jenis) }}
                        </span>
                    </td>
                    <td>{{ $transaksi->kategori ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $transaksi->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
        <p>Laporan ini dibuat otomatis oleh sistem. Semua data bersifat rahasia dan hanya untuk keperluan internal.</p>
    </div>
</body>

</html>
