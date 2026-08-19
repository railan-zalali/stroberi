<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan {{ $bulan }} {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
            background: #ffffff;
            padding: 0;
        }

        /* ─── KOP SURAT ─────────────────────────────────────────── */
        .kop {
            background: #16213e;
            padding: 20px 30px;
            margin-bottom: 0;
        }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { vertical-align: middle; }
        .kop-logo {
            width: 50px;
            height: 50px;
            background: #e94560;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 22px;
            font-weight: bold;
            color: #fff;
        }
        .kop-title {
            padding-left: 14px;
        }
        .kop-title h1 {
            font-size: 20px;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .kop-title p {
            font-size: 11px;
            color: #a8b2d8;
            margin-top: 2px;
        }
        .kop-meta {
            text-align: right;
        }
        .kop-meta p {
            font-size: 11px;
            color: #a8b2d8;
            line-height: 1.6;
        }

        /* ─── STRIPE BAWAH KOP ──────────────────────────────────── */
        .stripe {
            height: 4px;
            background: #e94560;
            margin-bottom: 24px;
        }

        /* ─── WRAPPER ───────────────────────────────────────────── */
        .body-wrap { padding: 0 30px 30px; }

        /* ─── SECTION TITLE ─────────────────────────────────────── */
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #16213e;
            border-left: 4px solid #e94560;
            padding-left: 10px;
            margin-bottom: 12px;
            margin-top: 24px;
        }

        /* ─── RINGKASAN (3 BOX) — table layout ──────────────────── */
        .summary-table { width: 100%; border-collapse: separate; border-spacing: 10px; }
        .summary-box {
            width: 33%;
            padding: 14px 16px;
            border-radius: 6px;
            border-left: 4px solid #ddd;
            vertical-align: top;
        }
        .box-income  { background: #f0fdf4; border-left-color: #16a34a; }
        .box-expense { background: #fef2f2; border-left-color: #dc2626; }
        .box-profit  { background: #eff6ff; border-left-color: #2563eb; }
        .box-loss    { background: #fff7ed; border-left-color: #ea580c; }

        .box-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            margin-bottom: 6px;
        }
        .box-value {
            font-size: 16px;
            font-weight: bold;
        }
        .income-color  { color: #16a34a; }
        .expense-color { color: #dc2626; }
        .profit-color  { color: #2563eb; }
        .profit-neg    { color: #dc2626; }
        .loss-color    { color: #ea580c; }

        /* ─── TABEL TRANSAKSI ────────────────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .data-table thead tr { background: #16213e; color: #fff; }
        .data-table th {
            padding: 9px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        .data-table th.right,
        .data-table td.right { text-align: right; }

        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .data-table tbody tr:nth-child(odd)  { background: #ffffff; }
        .data-table td { padding: 8px 10px; vertical-align: top; border-bottom: 1px solid #e2e8f0; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-income  { background: #dcfce7; color: #166534; }
        .badge-expense { background: #fee2e2; color: #991b1b; }

        .no-data {
            text-align: center;
            color: #9ca3af;
            padding: 20px;
            font-style: italic;
        }

        /* ─── FOOTER ─────────────────────────────────────────────── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 14px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        /* ─── TANDA TANGAN ───────────────────────────────────────── */
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .ttd-table td { width: 50%; vertical-align: top; text-align: center; font-size: 11px; }
        .ttd-line { border-bottom: 1px solid #333; width: 150px; margin: 60px auto 6px; }
    </style>
</head>

<body>

    {{-- KOP --}}
    <div class="kop">
        <table class="kop-table">
            <tr>
                <td style="width:60px;">
                    <div class="kop-logo">🍓</div>
                </td>
                <td class="kop-title">
                    <h1>Stroberi</h1>
                    <p>Sistem Manajemen Usaha Strawberi</p>
                </td>
                <td class="kop-meta">
                    <p><strong style="color:#fff;">LAPORAN KEUANGAN BULANAN</strong></p>
                    <p>Periode: {{ $bulan }} {{ $tahun }}</p>
                    <p>Dicetak: {{ now()->format('d F Y, H:i') }} WIB</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="stripe"></div>

    <div class="body-wrap">

        {{-- RINGKASAN --}}
        <div class="section-title">Ringkasan Keuangan</div>
        <table class="summary-table">
            <tr>
                <td class="summary-box box-income">
                    <div class="box-label">Total Pemasukan</div>
                    <div class="box-value income-color">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                </td>
                <td class="summary-box box-expense">
                    <div class="box-label">Total Pengeluaran</div>
                    <div class="box-value expense-color">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                </td>
                <td class="summary-box {{ $laba >= 0 ? 'box-profit' : 'box-expense' }}">
                    <div class="box-label">{{ $laba >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
                    <div class="box-value {{ $laba >= 0 ? 'profit-color' : 'profit-neg' }}">Rp {{ number_format(abs($laba), 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        {{-- RINCIAN KERUGIAN STOK --}}
        @if(isset($kerugianStok) && ($kerugianStok['total_nilai'] > 0 || count($kerugianStok['detail']) > 0))
        <div class="section-title">Rincian Kerugian Stok</div>
        <table class="summary-table">
            <tr>
                <td class="summary-box box-loss" style="width:50%">
                    <div class="box-label">Stok Rusak (kg)</div>
                    <div class="box-value loss-color">{{ number_format($kerugianStok['total_rusak_kg'], 2, ',', '.') }} kg</div>
                </td>
                <td class="summary-box box-loss" style="width:50%">
                    <div class="box-label">Estimasi Nilai Kerugian Stok</div>
                    <div class="box-value loss-color">Rp {{ number_format($kerugianStok['total_nilai'], 0, ',', '.') }}</div>
                </td>
            </tr>
        </table>

        @if(count($kerugianStok['detail']) > 0)
        <table class="data-table" style="margin-top:10px;">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Jenis</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Kadaluarsa</th>
                    <th>Penyebab Kerugian</th>
                    <th class="right">Qty Rusak (kg)</th>
                    <th class="right">Harga Beli/kg</th>
                    <th class="right">Estimasi Rugi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kerugianStok['detail'] as $item)
                <tr>
                    <td>{{ $item['batch'] }}</td>
                    <td>{{ ucfirst($item['jenis']) }}</td>
                    <td>{{ $item['tgl_masuk'] }}</td>
                    <td>{{ $item['tgl_kadaluarsa'] }}</td>
                    <td>
                        @if($item['stok_rusak'] > 0 && $item['stok_kadaluarsa'] > 0)
                            Rusak + Kadaluarsa
                        @elseif($item['stok_rusak'] > 0)
                            Stok Rusak
                        @else
                            Stok Kadaluarsa
                        @endif
                    </td>
                    <td class="right">{{ number_format($item['stok_rusak'] + $item['stok_kadaluarsa'], 2, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item['harga_beli'], 0, ',', '.') }}</td>
                    <td class="right" style="color:#ea580c; font-weight:bold;">Rp {{ number_format($item['nilai_rugi'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @endif

        {{-- DAFTAR TRANSAKSI --}}
        <div class="section-title">Daftar Transaksi</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th class="right">Jumlah</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $transaksi->jenis == 'pemasukan' ? 'badge-income' : 'badge-expense' }}">
                            {{ ucfirst($transaksi->jenis) }}
                        </span>
                    </td>
                    <td>{{ $transaksi->kategori ?? '-' }}</td>
                    <td class="right" style="font-weight:bold; color:{{ $transaksi->jenis == 'pemasukan' ? '#16a34a' : '#dc2626' }}">
                        Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                    </td>
                    <td>{{ $transaksi->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="no-data">Tidak ada data transaksi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <table class="ttd-table">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <div class="ttd-line"></div>
                    <p><strong>Pemilik Usaha</strong></p>
                </td>
                <td>
                    <p>Dibuat oleh,</p>
                    <div class="ttd-line"></div>
                    <p><strong>Admin / Kasir</strong></p>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh sistem Stroberi pada {{ now()->format('d F Y H:i') }} WIB.</p>
            <p>Bersifat rahasia — hanya untuk keperluan internal.</p>
        </div>

    </div>
</body>
</html>
