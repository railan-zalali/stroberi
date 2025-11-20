<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Strawberi;
use App\Models\Supplier;
use Carbon\Carbon;

class StrawberiSeeder extends Seeder
{
    public function run(): void
    {
        $supplierA = Supplier::where('nama', 'Supplier A')->first();
        $supplierB = Supplier::where('nama', 'Supplier B')->first();

        $now = Carbon::now()->format('Y-m-d');

        $data = [
            ['jenis' => 'segar', 'grade' => 'a', 'jumlah' => 10, 'harga_beli' => 50000, 'supplier_id' => optional($supplierA)->id, 'keterangan' => 'Seed segar A'],
            ['jenis' => 'segar', 'grade' => 'b', 'jumlah' => 8, 'harga_beli' => 45000, 'supplier_id' => optional($supplierA)->id, 'keterangan' => 'Seed segar B'],
            ['jenis' => 'segar', 'grade' => 'c', 'jumlah' => 6, 'harga_beli' => 40000, 'supplier_id' => optional($supplierB)->id, 'keterangan' => 'Seed segar C'],
            ['jenis' => 'beku', 'grade' => 'a', 'jumlah' => 12, 'harga_beli' => 55000, 'supplier_id' => optional($supplierB)->id, 'keterangan' => 'Seed beku A'],
            ['jenis' => 'beku', 'grade' => 'b', 'jumlah' => 9, 'harga_beli' => 50000, 'supplier_id' => optional($supplierA)->id, 'keterangan' => 'Seed beku B'],
            ['jenis' => 'beku', 'grade' => 'c', 'jumlah' => 7, 'harga_beli' => 45000, 'supplier_id' => optional($supplierB)->id, 'keterangan' => 'Seed beku C'],
        ];

        foreach ($data as $item) {
            Strawberi::create([
                'jenis' => $item['jenis'],
                'grade' => $item['grade'],
                'jumlah' => $item['jumlah'],
                'harga_beli' => $item['harga_beli'],
                'tanggal_masuk' => $now,
                'supplier_id' => $item['supplier_id'],
                'keterangan' => $item['keterangan'],
                'is_posted' => true,
            ]);
        }
    }
}