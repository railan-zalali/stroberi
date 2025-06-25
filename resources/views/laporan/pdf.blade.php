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
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            font-size: 16px;
        }

        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .summary-item {
            width: 30%;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .summary-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
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
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
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
