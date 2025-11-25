<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Transaksi;

class SupplierPaymentFlowTest extends TestCase
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
            'status' => 'aktif'
        ]);
    }

    /** @test */
    public function it_shows_unpaid_purchases_in_supplier_details()
    {
        $this->actingAs($this->user);

        // Create unpaid purchase transaction
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 500000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 10kg strawberi segar grade A',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => false,
        ]);

        $response = $this->get(route('supplier.show', $this->supplier));

        $response->assertStatus(200);
        $response->assertSee('Pembelian Belum Dibayar');
        $response->assertSee('Rp 500.000');
        $response->assertSee('Bayar'); // Button to mark as paid
    }

    /** @test */
    public function it_can_mark_transaction_as_paid()
    {
        $this->actingAs($this->user);

        // Create unpaid purchase transaction
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 750000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 15kg strawberi beku grade B',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => false,
        ]);

        $response = $this->post(route('supplier.mark-paid', [$this->supplier, $transaksi]));

        $response->assertRedirect(route('supplier.show', $this->supplier));
        $response->assertSessionHas('success', 'Transaksi berhasil ditandai sebagai dibayar.');

        // Verify transaction is marked as paid
        $transaksi->refresh();
        $this->assertTrue($transaksi->is_paid);
        $this->assertNotNull($transaksi->paid_at);
        $this->assertEquals($this->user->id, $transaksi->paid_by);
    }

    /** @test */
    public function it_does_not_show_paid_transactions_in_unpaid_section()
    {
        $this->actingAs($this->user);

        // Create paid purchase transaction
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 300000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 5kg strawberi segar grade A',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $this->user->id,
        ]);

        $response = $this->get(route('supplier.show', $this->supplier));

        $response->assertStatus(200);
        $response->assertDontSee('Pembelian Belum Dibayar');
        $response->assertSee('Tidak ada pembelian yang belum dibayar.');
    }

    /** @test */
    public function it_shows_payment_status_in_transaction_list()
    {
        $this->actingAs($this->user);

        // Create both paid and unpaid transactions
        $unpaidTransaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 400000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 8kg strawberi segar',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => false,
        ]);

        $paidTransaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 600000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 12kg strawberi beku',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $this->user->id,
        ]);

        $response = $this->get(route('supplier.show', $this->supplier));

        $response->assertStatus(200);

        // Check unpaid transaction shows "Belum Dibayar" status
        $response->assertSee('Belum Dibayar');

        // Check paid transaction shows "Sudah Dibayar" status
        $response->assertSee('Sudah Dibayar');

        // Check paid date is shown
        $response->assertSee($paidTransaksi->paid_at->format('d/m/Y'));
    }

    /** @test */
    public function it_prevents_marking_non_purchase_transactions_as_paid()
    {
        $this->actingAs($this->user);

        // Create a loan transaction (should not be markable as paid)
        $transaksi = Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 1000000,
            'tanggal' => now(),
            'keterangan' => 'Pinjaman ke supplier',
            'user_id' => $this->user->id,
            'kategori' => 'Pinjaman',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pinjaman',
            'is_pinjaman' => true,
            'is_paid' => false,
        ]);

        $response = $this->post(route('supplier.mark-paid', [$this->supplier, $transaksi]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Transaksi ini tidak dapat ditandai sebagai dibayar.');

        // Verify transaction is still unpaid
        $transaksi->refresh();
        $this->assertFalse($transaksi->is_paid);
    }

    /** @test */
    public function it_calculates_unpaid_purchases_correctly()
    {
        $this->actingAs($this->user);

        // Create multiple transactions with different statuses
        Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 200000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 4kg - unpaid',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => false,
        ]);

        Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 300000,
            'tanggal' => now(),
            'keterangan' => 'Pembelian 6kg - paid',
            'user_id' => $this->user->id,
            'kategori' => 'Pembelian Strawberi',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pembelian',
            'is_pinjaman' => false,
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $this->user->id,
        ]);

        // Create a loan transaction (should not be included in unpaid calculation)
        Transaksi::create([
            'jenis' => 'pengeluaran',
            'jumlah' => 500000,
            'tanggal' => now(),
            'keterangan' => 'Pinjaman ke supplier',
            'user_id' => $this->user->id,
            'kategori' => 'Pinjaman',
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->nama,
            'tipe_transaksi' => 'pinjaman',
            'is_pinjaman' => true,
            'is_paid' => false,
        ]);

        $response = $this->get(route('supplier.show', $this->supplier));

        $response->assertStatus(200);

        // Should only show Rp 200.000 as unpaid (the unpaid purchase)
        // The paid purchase (Rp 300.000) and loan (Rp 500.000) should not be included
        $response->assertSee('Rp 200.000'); // This is the unpaid amount
        $response->assertSee('Total Nilai Belum Dibayar: Rp');
        $response->assertSee('200.000');
    }
}
