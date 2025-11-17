<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; margin: 0 0 6px 0; }
        h2 { font-size: 16px; margin: 16px 0 8px 0; }
        .meta { margin-bottom: 10px; }
        .chip { display: inline-block; padding: 2px 8px; border: 1px solid #bbb; border-radius: 12px; font-size: 11px; margin-right: 6px; color: #555; }
        .summary { width: 100%; margin: 10px 0 16px 0; border-collapse: collapse; }
        .summary td { padding: 6px 8px; border: 1px solid #ddd; }
        .summary .label { background: #f9fafb; width: 40%; }
        .summary .value { text-align: right; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f3f4f6; color: #111827; font-weight: 600; border: 1px solid #e5e7eb; padding: 8px; }
        tbody td { border: 1px solid #e5e7eb; padding: 6px; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        .text-right { text-align: right; }
        .small { font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <h1>Laporan Transaksi</h1>
    <div class="meta">
        <span class="chip">Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d/m/Y') }}</span>
        @if(!empty($jenis))
            <span class="chip">Jenis: {{ ucfirst($jenis) }}</span>
        @endif
        @if(!empty($kategori))
            <span class="chip">Kategori: {{ $kategori }}</span>
        @endif
    </div>

    @php
        $totalPemasukan = $transaksis->where('jenis','pemasukan')->sum('jumlah');
        $totalPengeluaran = $transaksis->where('jenis','pengeluaran')->sum('jumlah');
        $laba = $totalPemasukan - $totalPengeluaran;
    @endphp

    <table class="summary">
        <tr>
            <td class="label">Total Pemasukan</td>
            <td class="value">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Pengeluaran</td>
            <td class="value">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Laba Bersih</td>
            <td class="value">Rp {{ number_format($laba, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h2>Detail Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 90px;">Tanggal</th>
                <th style="width: 90px;">Jenis</th>
                <th style="width: 140px;">Kategori</th>
                <th style="width: 120px;" class="text-right">Jumlah (Rp)</th>
                <th style="width: 160px;">Keterangan</th>
                <th style="width: 120px;">Pihak</th>
                <th style="width: 90px;">Tipe</th>
                <th style="width: 75px;">Pinjaman</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($t->jenis) }}</td>
                    <td>{{ $t->kategori ?? '-' }}</td>
                    <td class="text-right">{{ number_format($t->jumlah, 0, ',', '.') }}</td>
                    <td>{{ $t->keterangan ?? '-' }}</td>
                    <td>{{ $t->supplier_name ?? ($t->supplier->nama ?? '-') }}</td>
                    <td>{{ $t->tipe_transaksi ?? '-' }}</td>
                    <td>{{ $t->is_pinjaman ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-right small">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="small">Dicetak pada {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>