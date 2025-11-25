<?php

namespace Tests\Feature;

use App\Models\Strawberi;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SupplierStockUnlockTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $supplier;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create([
            'nama' => 'Supplier Test',
            'alamat' => 'Jl. Test No. 123',
            'telepon' => '081234567890',
            'email' => 'supplier@test.com',
            'status' => 'aktif'
        ]);
    }

    /** @test */
    public function it_unlocks_locked_stock_when_supplier_is_paid()
    {
        // Buat stok strawberry dengan supplier
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stok_awal' => 100,
            'stok_terjual' => 0,
            'stok_terkunci' => 50, // Stok terkunci
            'is_locked' => true,
            'batch_number' => 'BATCH-001',
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
            'harga_beli' => 10000,
            'is_posted' => true
        ]);

        // Buat transaksi pembelian yang belum dibayar
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 1000000,
            'tanggal' => now(),
            'kategori' => 'Pembelian Strawberi',
            'keterangan' => 'Pembelian batch BATCH-001',
            'user_id' => $this->user->id,
            'supplier_id' => $this->supplier->id,
            'tipe_transaksi' => 'pembelian',
            'is_paid' => false,
            'is_pinjaman' => false
        ]);

        // Login sebagai user
        $this->actingAs($this->user);

        // Tandai transaksi sebagai dibayar
        $response = $this->post(route('supplier.mark-paid', [
            'supplier' => $this->supplier->id,
            'transaksi' => $transaksi->id
        ]));

        // Verifikasi redirect dan pesan sukses
        $response->assertRedirect(route('supplier.show', $this->supplier));
        $response->assertSessionHas('success', 'Transaksi berhasil ditandai sebagai dibayar.');

        // Verifikasi bahwa transaksi sudah dibayar
        $this->assertTrue($transaksi->fresh()->is_paid);
        $this->assertNotNull($transaksi->fresh()->paid_at);
        $this->assertEquals($this->user->id, $transaksi->fresh()->paid_by);

        // Verifikasi bahwa stok sudah tidak terkunci lagi
        $strawberi->refresh();
        $this->assertEquals(0, $strawberi->stok_terkunci);
        $this->assertFalse($strawberi->is_locked);
    }

    /** @test */
    public function it_only_unlocks_stock_from_the_specific_supplier()
    {
        // Buat supplier lain
        $otherSupplier = Supplier::factory()->create(['nama' => 'Other Supplier']);

        // Buat stok untuk supplier utama dengan kunci
        $mainSupplierStock = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stok_awal' => 100,
            'stok_terkunci' => 50,
            'is_locked' => true,
            'batch_number' => 'BATCH-001',
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
            'harga_beli' => 10000,
            'is_posted' => true
        ]);

        // Buat stok untuk supplier lain dengan kunci
        $otherSupplierStock = Strawberi::factory()->create([
            'supplier_id' => $otherSupplier->id,
            'stok_awal' => 100,
            'stok_terkunci' => 30,
            'is_locked' => true,
            'batch_number' => 'BATCH-002',
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
            'harga_beli' => 10000,
            'is_posted' => true
        ]);

        // Buat transaksi pembelian untuk supplier utama
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 1000000,
            'tanggal' => now(),
            'kategori' => 'Pembelian Strawberi',
            'user_id' => $this->user->id,
            'supplier_id' => $this->supplier->id,
            'is_paid' => false
        ]);

        $this->actingAs($this->user);

        // Bayar transaksi supplier utama
        $this->post(route('supplier.mark-paid', [
            'supplier' => $this->supplier->id,
            'transaksi' => $transaksi->id
        ]));

        // Verifikasi bahwa hanya stok dari supplier utama yang dibuka kuncinya
        $mainSupplierStock->refresh();
        $otherSupplierStock->refresh();

        $this->assertEquals(0, $mainSupplierStock->stok_terkunci);
        $this->assertFalse($mainSupplierStock->is_locked);

        $this->assertEquals(30, $otherSupplierStock->stok_terkunci);
        $this->assertTrue($otherSupplierStock->is_locked);
    }

    /** @test */
    public function it_does_not_unlock_stock_if_payment_fails()
    {
        // Buat stok dengan kunci
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'stok_awal' => 100,
            'stok_terkunci' => 50,
            'is_locked' => true,
            'batch_number' => 'BATCH-001',
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
            'harga_beli' => 10000,
            'is_posted' => true
        ]);

        // Buat transaksi yang sudah dibayar (tidak bisa dibayar lagi)
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 1000000,
            'tanggal' => now(),
            'kategori' => 'Pembelian Strawberi',
            'user_id' => $this->user->id,
            'supplier_id' => $this->supplier->id,
            'is_paid' => true // Sudah dibayar
        ]);

        $this->actingAs($this->user);

        // Coba bayar transaksi yang sudah dibayar
        $response = $this->post(route('supplier.mark-paid', [
            'supplier' => $this->supplier->id,
            'transaksi' => $transaksi->id
        ]));

        // Verifikasi error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Transaksi ini tidak dapat ditandai sebagai dibayar.');

        // Verifikasi bahwa stok tetap terkunci
        $strawberi->refresh();
        $this->assertEquals(50, $strawberi->stok_terkunci);
        $this->assertTrue($strawberi->is_locked);
    }
}
