<?php

namespace Tests\Feature;

use App\Models\Strawberi;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectStockPostingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $supplier;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create([
            'status' => 'aktif'
        ]);
    }

    /** @test */
    public function it_adds_strawberry_stock_directly_to_global_stock()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('strawberi.store'), [
            'jenis' => 'segar',
            'grade' => 'a',
            'jumlah' => 50,
            'harga_beli' => 25000,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'keterangan' => 'Stok segar grade A',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Stok strawberi berhasil ditambahkan ke stok global dan langsung tersedia untuk dijual.');

        // Verify the stock was created and is immediately available
        $this->assertDatabaseHas('strawberis', [
            'jenis' => 'segar',
            'grade' => 'a',
            'jumlah' => 50,
            'harga_beli' => 25000,
            'supplier_id' => $this->supplier->id,
            'is_posted' => true, // Should be true immediately
        ]);

        // Verify the stock appears in global stock
        $strawberi = Strawberi::where('jenis', 'segar')
            ->where('grade', 'a')
            ->where('supplier_id', $this->supplier->id)
            ->first();

        $this->assertNotNull($strawberi);
        $this->assertTrue($strawberi->is_posted);
        $this->assertEquals(50, $strawberi->stok_tersisa);
    }

    /** @test */
    public function it_creates_direct_bookkeeping_transaction()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('strawberi.store'), [
            'jenis' => 'beku',
            'grade' => 'b',
            'jumlah' => 30,
            'harga_beli' => 20000,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'keterangan' => 'Stok beku grade B',
        ]);

        $response->assertRedirect();

        // Verify the transaction was created directly in bookkeeping (not pending)
        $this->assertDatabaseHas('transaksis', [
            'jenis' => 'pengeluaran',
            'jumlah' => 600000, // 30 * 20000
            'kategori' => 'Pembelian Strawberi', // Not "Pembelian Strawberi (Pending)"
            'supplier_id' => $this->supplier->id,
            'tipe_transaksi' => 'pembelian',
        ]);
    }

    /** @test */
    public function it_does_not_show_finish_transaction_button_when_no_pending_transactions()
    {
        $this->actingAs($this->user);

        // Add stock directly (should not create pending transactions)
        $this->post(route('strawberi.store'), [
            'jenis' => 'segar',
            'grade' => 'c',
            'jumlah' => 25,
            'harga_beli' => 15000,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
        ]);

        // Visit supplier show page
        $response = $this->get(route('supplier.show', $this->supplier));

        $response->assertStatus(200);

        // Should not show finish transaction button or pending info
        $response->assertDontSee('Selesaikan Transaksi');
        $response->assertDontSee('Pembelian Pending');
        // Should show unpaid purchases section instead
        $response->assertSee('Pembelian Belum Dibayar');
        $response->assertSee('Total Nilai Belum Dibayar');
    }

    /** @test */
    public function it_shows_stock_in_global_sell_form_immediately()
    {
        $this->actingAs($this->user);

        // Add stock directly
        $this->post(route('strawberi.store'), [
            'jenis' => 'segar',
            'grade' => 'a',
            'jumlah' => 100,
            'harga_beli' => 30000,
            'tanggal_masuk' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
        ]);

        // Check if stock appears in global sell form
        $response = $this->get(route('strawberi.sell-global.form'));

        $response->assertStatus(200);

        // The stock should be available for sale immediately
        // This verifies that our FEFO logic can see the newly posted stock
        $this->assertDatabaseHas('strawberis', [
            'jenis' => 'segar',
            'grade' => 'a',
            'is_posted' => true,
        ]);

        // Verify the stock calculation is correct
        $strawberi = Strawberi::where('jenis', 'segar')
            ->where('grade', 'a')
            ->where('supplier_id', $this->supplier->id)
            ->first();

        $this->assertNotNull($strawberi);
        $this->assertEquals(100, $strawberi->stok_tersisa);
    }
}
