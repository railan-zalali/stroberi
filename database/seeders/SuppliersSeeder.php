<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::updateOrCreate(
            ['nama' => 'Supplier A'],
            ['status' => 'aktif', 'alamat' => 'Jl. Mawar No.1', 'telepon' => '081234567890']
        );

        Supplier::updateOrCreate(
            ['nama' => 'Supplier B'],
            ['status' => 'aktif', 'alamat' => 'Jl. Melati No.2', 'telepon' => '081298765432']
        );
    }
}