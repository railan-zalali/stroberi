<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigratePinjamanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Memigrasikan data pinjaman dari tabel suppliers ke tabel transaksis
     */
    public function run(): void
    {
        // Ambil semua supplier yang memiliki pinjaman atau pembayaran
        $suppliers = Supplier::where('total_pinjaman', '>', 0)
            ->orWhere('total_pembayaran', '>', 0)
            ->get();

        DB::beginTransaction();
        try {
            foreach ($suppliers as $supplier) {
                // Jika ada pinjaman, buat transaksi pinjaman
                if ($supplier->total_pinjaman > 0) {
                    Transaksi::create([
                        'jenis' => 'pengeluaran',
                        'jumlah' => $supplier->total_pinjaman,
                        'tanggal' => Carbon::now()->subDays(30), // Asumsi pinjaman dibuat 30 hari yang lalu
                        'kategori' => 'Pinjaman Supplier',
                        'keterangan' => "Migrasi data pinjaman supplier {$supplier->nama}",
                        'user_id' => 1, // Asumsi user ID 1 adalah admin
                        'supplier_id' => $supplier->id,
                        'tipe_transaksi' => 'pinjaman',
                        'is_pinjaman' => true,
                    ]);
                }

                // Jika ada pembayaran, buat transaksi pembayaran
                if ($supplier->total_pembayaran > 0) {
                    Transaksi::create([
                        'jenis' => 'pengeluaran',
                        'jumlah' => $supplier->total_pembayaran,
                        'tanggal' => Carbon::now()->subDays(15), // Asumsi pembayaran dibuat 15 hari yang lalu
                        'kategori' => 'Pembayaran Supplier',
                        'keterangan' => "Migrasi data pembayaran supplier {$supplier->nama}",
                        'user_id' => 1, // Asumsi user ID 1 adalah admin
                        'supplier_id' => $supplier->id,
                        'tipe_transaksi' => 'pembayaran',
                        'is_pinjaman' => false,
                    ]);
                }
            }

            DB::commit();
            $this->command->info('Data pinjaman berhasil dimigrasi ke tabel transaksis');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal migrasi data: ' . $e->getMessage());
        }
    }
}
