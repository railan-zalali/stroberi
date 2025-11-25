<?php

namespace Database\Factories;

use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory
{
    protected $model = Transaksi::class;

    public function definition(): array
    {
        $jenis = $this->faker->randomElement(['pemasukan', 'pengeluaran']);
        $isPinjaman = $this->faker->boolean(20); // 20% chance of being a loan transaction
        
        return [
            'jenis' => $jenis,
            'jumlah' => $this->faker->numberBetween(10000, 1000000),
            'tanggal' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'kategori' => $this->faker->randomElement([
                'Penjualan',
                'Pembelian',
                'Operasional',
                'Gaji',
                'Pinjaman',
                'Pengembalian Pinjaman',
                'Pembelian Strawberi (Pending)',
            ]),
            'keterangan' => $this->faker->optional()->sentence(),
            'is_pinjaman' => $isPinjaman,
            'tipe_transaksi' => $isPinjaman ? $this->faker->randomElement(['pinjaman', 'pengembalian']) : null,
            'user_id' => \App\Models\User::factory(),
        ];
    }

    public function pemasukan(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'pemasukan',
        ]);
    }

    public function pengeluaran(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'pengeluaran',
        ]);
    }

    public function pinjaman(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pinjaman',
            'jenis' => 'pengeluaran',
            'kategori' => 'Pinjaman',
        ]);
    }

    public function pengembalian(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pengembalian',
            'jenis' => 'pemasukan',
            'kategori' => 'Pengembalian Pinjaman',
        ]);
    }
}