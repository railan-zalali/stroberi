<?php

namespace Database\Factories;

use App\Models\Strawberi;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class StrawberiFactory extends Factory
{
    protected $model = Strawberi::class;

    public function definition(): array
    {
        $jumlah = $this->faker->numberBetween(10, 200);
        return [
            'supplier_id' => Supplier::factory(),
            'jenis' => $this->faker->randomElement(['segar', 'beku']),
            'grade' => $this->faker->randomElement(['A', 'B', 'C', 'campur']),
            'jumlah' => $jumlah,
            'stok_awal' => $jumlah,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_adjustment' => 0,
            'stok_terkunci' => 0,
            'harga_beli' => $this->faker->numberBetween(15000, 35000),
            'tanggal_masuk' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'tanggal_kadaluarsa' => $this->faker->dateTimeBetween('now', '+14 days'),
            'keterangan' => $this->faker->optional()->sentence(),
            'is_locked' => false,
            'is_posted' => true,
            'batch_number' => 'BTH-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4)),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_locked' => true,
            'stok_terkunci' => fn(array $attributes) => $attributes['stok_awal'] / 2,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_posted' => false,
        ]);
    }
}
