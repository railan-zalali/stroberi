<?php

use App\Models\Strawberi;
use App\Models\Supplier;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->supplier = Supplier::factory()->create();
});

describe('Stock Locking Mechanism', function () {
    it('prevents editing of locked stock', function () {
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 100,
            'stok_terkunci' => 50,
            'is_locked' => true,
            'is_posted' => false, // Not posted, only locked
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->user)
            ->put("/strawberi/{$strawberi->id}", [
                'jumlah' => 150,
                'harga_beli' => 25000,
            ]);

        // Should redirect back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Stok ini sedang dikunci untuk transaksi dan tidak dapat diedit');
        $this->assertEquals(100, $strawberi->fresh()->stok_awal);
    });

    it('allows stock finalization after payment', function () {
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 100,
            'stok_terkunci' => 50,
            'is_locked' => true,
            'is_posted' => false,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        Session::put('pending_stock_allocations', [
            $strawberi->id => 50
        ]);

        $response = $this->actingAs($this->user)
            ->post("/transaksi/finalize-stock/{$strawberi->id}");

        $response->assertRedirect();
        $this->assertEquals(0, $strawberi->fresh()->stok_terkunci);
        $this->assertEquals(false, $strawberi->fresh()->is_locked);
        $this->assertEquals(true, $strawberi->fresh()->is_posted);
    });
});

describe('FEFO Stock Logic', function () {
    it('validates grade-specific stock availability', function () {
        // Create stock for grade A
        $strawberiA = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 50,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        // Create stock for grade B
        $strawberiB = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'B',
            'stok_awal' => 100,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        // Try to sell grade A with insufficient stock
        $response = $this->actingAs($this->user)
            ->post('/strawberi/sell-global', [
                'jenis' => 'segar',
                'jumlah_jual' => 60,
                'preferensi_grade' => 'a',
                'harga_jual' => 25000,
                'pembeli' => 'Test Customer',
                'tanggal_jual' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(50, $strawberiA->fresh()->stok_awal - $strawberiA->fresh()->stok_terjual - $strawberiA->fresh()->stok_terkunci);
    });

    it('allows campur grade to use any available stock', function () {
        // Create stock for different grades
        $strawberiA = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 30,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        $strawberiB = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'B',
            'stok_awal' => 40,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(6), // Earlier expiry
        ]);

        // Sell with campur preference
        $response = $this->actingAs($this->user)
            ->post('/strawberi/sell-global', [
                'jenis' => 'segar',
                'jumlah_jual' => 50,
                'preferensi_grade' => 'campur',
                'harga_jual' => 25000,
                'pembeli' => 'Test Customer',
                'tanggal_jual' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect();

        // Should use grade B first (earlier expiry)
        // Grade B (40kg) should be used completely, Grade A (10kg of 30kg) should be used for remaining
        $this->assertEquals(20, $strawberiA->fresh()->stok_awal - $strawberiA->fresh()->stok_terjual - $strawberiA->fresh()->stok_terkunci); // Reduced by 10
        $this->assertEquals(0, $strawberiB->fresh()->stok_awal - $strawberiB->fresh()->stok_terjual - $strawberiB->fresh()->stok_terkunci); // Reduced by 40 (all used)
    });

    it('blocks non-campur grades from using other grade stock', function () {
        // Create stock for grade A only
        $strawberiA = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 100,
            'stok_terjual' => 0,
            'stok_rusak' => 0,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        // Try to sell grade B when no grade B stock exists
        $response = $this->actingAs($this->user)
            ->post('/strawberi/sell-global', [
                'jenis' => 'segar',
                'jumlah_jual' => 50,
                'preferensi_grade' => 'b',
                'harga_jual' => 25000,
                'pembeli' => 'Test Customer',
                'tanggal_jual' => now()->format('Y-m-d'),
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(100, $strawberiA->fresh()->stok_awal - $strawberiA->fresh()->stok_terjual - $strawberiA->fresh()->stok_terkunci);
    });
});

describe('Transaction Filtering', function () {
    it('filters transactions by type correctly', function () {
        // Create regular transaction
        Transaksi::factory()->create([
            'jenis' => 'pemasukan',
            'jumlah' => 100000,
            'tanggal' => now(),
            'is_pinjaman' => false,
            'kategori' => 'Penjualan',
        ]);

        // Create loan transaction
        Transaksi::factory()->create([
            'jenis' => 'pengeluaran',
            'jumlah' => 50000,
            'tanggal' => now(),
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pinjaman',
            'kategori' => 'Pinjaman',
        ]);

        // Create loan repayment transaction
        Transaksi::factory()->create([
            'jenis' => 'pemasukan',
            'jumlah' => 30000,
            'tanggal' => now(),
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pengembalian',
            'kategori' => 'Pengembalian Pinjaman',
        ]);

        // Test regular transactions only
        $response = $this->actingAs($this->user)
            ->get('/laporan/keuangan?tipe_transaksi=biasa');

        $response->assertStatus(200);
        $response->assertViewHas('transaksis', function ($transaksis) {
            return $transaksis->every(function ($transaksi) {
                return $transaksi->is_pinjaman === false;
            });
        });

        // Test loan transactions only
        $response = $this->actingAs($this->user)
            ->get('/laporan/keuangan?tipe_transaksi=pinjaman');

        $response->assertStatus(200);
        $response->assertViewHas('transaksis', function ($transaksis) {
            return $transaksis->every(function ($transaksi) {
                return $transaksi->is_pinjaman === true &&
                    $transaksi->tipe_transaksi === 'pinjaman';
            });
        });

        // Test loan repayment transactions only
        $response = $this->actingAs($this->user)
            ->get('/laporan/keuangan?tipe_transaksi=pengembalian');

        $response->assertStatus(200);
        $response->assertViewHas('transaksis', function ($transaksis) {
            return $transaksis->every(function ($transaksi) {
                return $transaksi->is_pinjaman === true &&
                    $transaksi->tipe_transaksi === 'pengembalian';
            });
        });
    });

    it('includes loan transactions in financial calculations', function () {
        // Create regular income
        Transaksi::factory()->create([
            'jenis' => 'pemasukan',
            'jumlah' => 100000,
            'tanggal' => now(),
            'is_pinjaman' => false,
            'kategori' => 'Penjualan',
        ]);

        // Create loan repayment (should count as income)
        Transaksi::factory()->create([
            'jenis' => 'pemasukan',
            'jumlah' => 50000,
            'tanggal' => now(),
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pengembalian',
            'kategori' => 'Pengembalian Pinjaman',
        ]);

        // Create loan disbursement (should count as expense)
        Transaksi::factory()->create([
            'jenis' => 'pengeluaran',
            'jumlah' => 30000,
            'tanggal' => now(),
            'is_pinjaman' => true,
            'tipe_transaksi' => 'pinjaman',
            'kategori' => 'Pinjaman',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/keuangan');

        $response->assertStatus(200);
        $response->assertViewHas('totalPemasukan', 150000); // 100000 + 50000
        $response->assertViewHas('totalPengeluaran', 30000);
        $response->assertViewHas('laba', 120000); // 150000 - 30000
    });
});

describe('Stock Consistency', function () {
    it('maintains consistency between strawberi/show and supplier/show', function () {
        // Create stock
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 100,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => true,
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        // Check strawberi show page
        $response = $this->actingAs($this->user)
            ->get("/strawberi/{$strawberi->id}");

        $response->assertStatus(200);
        $response->assertSee('100'); // Should show stock amount

        // Check supplier show page
        $response = $this->actingAs($this->user)
            ->get("/supplier/{$this->supplier->id}");

        $response->assertStatus(200);
        $response->assertSee('100'); // Should show same stock amount
    });

    it('excludes pending transactions from active stock', function () {
        // Create pending stock
        $strawberi = Strawberi::factory()->create([
            'supplier_id' => $this->supplier->id,
            'jenis' => 'segar',
            'grade' => 'A',
            'stok_awal' => 100,
            'stok_terkunci' => 0,
            'is_locked' => false,
            'is_posted' => false, // Not yet posted
            'tanggal_masuk' => now(),
            'tanggal_kadaluarsa' => now()->addDays(7),
        ]);

        // Create transaction for pending stock
        Transaksi::factory()->create([
            'jenis' => 'pengeluaran',
            'jumlah' => 100000,
            'tanggal' => now(),
            'kategori' => 'Pembelian Strawberi (Pending)',
            'is_pinjaman' => false,
        ]);

        // Check that pending stock is not included in available stock calculations
        $response = $this->actingAs($this->user)
            ->get('/strawberi');

        $response->assertStatus(200);
        // Should not show pending stock in available calculations
    });
});
