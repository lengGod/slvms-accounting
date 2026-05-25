<?php

namespace Tests\Feature;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($this->user);
    }

    public function test_deleting_piutang_reverses_balance_and_titipan()
    {
        $debtor = Debtor::factory()->create(['initial_balance' => 0]);
        
        // Add some titipan first
        Titipan::create([
            'debtor_id' => $debtor->id,
            'amount' => 1000,
            'tanggal' => now(),
            'keterangan' => 'Initial Titipan',
            'user_id' => $this->user->id
        ]);

        $debtor = $debtor->fresh();
        $this->assertEquals(1000, $debtor->total_titipan);

        // Create piutang that uses titipan
        $response = $this->post(route('transactions.use-titipan-for-piutang'), [
            'debtor_id' => $debtor->id,
            'amount' => 500,
            'bagi_pokok' => 500,
            'bagi_hasil' => 0,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Test Piutang'
        ]);

        $response->assertRedirect();

        $debtor = $debtor->fresh();
        // Piutang 500, Payment 500 -> Current balance 0
        $this->assertEquals(0, $debtor->current_balance);
        // Titipan 1000 - 500 = 500
        $this->assertEquals(500, $debtor->total_titipan);

        $piutangTx = Transaction::where('type', 'piutang')->first();
        $this->assertNotNull($piutangTx);
        
        // Delete the piutang
        $response = $this->delete(route('transactions.destroy', $piutangTx->id));
        $response->assertRedirect();

        $debtor = $debtor->fresh();

        $this->assertEquals(0, Transaction::count(), 'All transactions should be deleted');
        $this->assertEquals(0, $debtor->current_balance, 'Balance should be 0');
        $this->assertEquals(1000, $debtor->total_titipan, 'Titipan should be restored to 1000');
    }

    public function test_deleting_payment_with_excess_reverses_titipan()
    {
        $debtor = Debtor::factory()->create(['initial_balance' => 0]);
        
        // Create piutang 500
        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'piutang',
            'amount' => -500,
            'bagi_pokok' => -500,
            'bagi_hasil' => 0,
            'transaction_date' => now(),
            'user_id' => $this->user->id
        ]);

        // Pay 1000 (500 pays debt, 500 becomes titipan)
        $response = $this->post(route('transactions.store'), [
            'debtor_id' => $debtor->id,
            'type' => 'pembayaran',
            'amount' => 1000,
            'bagi_pokok' => 500,
            'bagi_hasil' => 0,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Payment with excess'
        ]);

        $response->assertRedirect();

        $debtor = $debtor->fresh();
        $this->assertEquals(500, $debtor->total_titipan);
        // Balance should be 0 (Lunas) because 500 debt was paid by 500 of the 1000 payment.
        $this->assertEquals(0, $debtor->current_balance); 

        $paymentTx = Transaction::where('type', 'pembayaran')->first();
        
        // Delete the payment
        $this->delete(route('transactions.destroy', $paymentTx->id));

        $debtor = $debtor->fresh();
        
        // Titipan should be 0 again
        $this->assertEquals(0, $debtor->total_titipan);
        // Balance should be -500 again (the original piutang remains)
        $this->assertEquals(-500, $debtor->current_balance);
    }

    public function test_deleting_automatic_payment_reverses_titipan_deduction()
    {
        $debtor = Debtor::factory()->create(['initial_balance' => 0]);
        
        Titipan::create([
            'debtor_id' => $debtor->id,
            'amount' => 1000,
            'tanggal' => now(),
            'keterangan' => 'Initial Titipan',
            'user_id' => $this->user->id
        ]);

        // Create piutang that uses titipan
        $this->post(route('transactions.use-titipan-for-piutang'), [
            'debtor_id' => $debtor->id,
            'amount' => 500,
            'bagi_pokok' => 500,
            'bagi_hasil' => 0,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Test Piutang'
        ]);

        $debtor = $debtor->fresh();
        $this->assertEquals(500, $debtor->total_titipan);

        $paymentTx = Transaction::where('type', 'pembayaran')->first();
        $this->assertStringContainsString('Pembayaran otomatis', $paymentTx->description);

        // Delete the automatic payment
        $this->delete(route('transactions.destroy', $paymentTx->id));

        $debtor = $debtor->fresh();
        // Titipan should be 1000 again (deduction reversed)
        $this->assertEquals(1000, $debtor->total_titipan);
        // Current balance should be -500 (piutang remains)
        $this->assertEquals(-500, $debtor->current_balance);
    }

    public function test_titipan_allocation_matches_input_exactly()
    {
        $debtor = Debtor::factory()->create(['initial_balance' => 0]);
        
        // Add large titipan
        Titipan::create([
            'debtor_id' => $debtor->id,
            'amount' => 110000,
            'tanggal' => now(),
            'keterangan' => 'Large Titipan',
            'user_id' => $this->user->id
        ]);

        // Create piutang 11000 (Pokok 1000, Hasil 10000)
        // User reported: Piutang 11000 with input Hasil 10000 and Pokok 10000 (Total input 20000?)
        // Wait, if amount is 11000, but sum of parts is 20000, that's invalid input.
        // Let's assume input matches total: Hasil 10000, Pokok 1000.
        
        $this->post(route('transactions.use-titipan-for-piutang'), [
            'debtor_id' => $debtor->id,
            'amount' => 11000,
            'bagi_pokok' => 1000,
            'bagi_hasil' => 10000,
            'transaction_date' => now()->format('Y-m-d'),
            'description' => 'Exact Allocation Test'
        ]);

        $paymentTx = Transaction::where('type', 'pembayaran')->first();
        
        // Should match input exactly, not some ratio
        $this->assertEquals(1000, $paymentTx->bagi_pokok);
        $this->assertEquals(10000, $paymentTx->bagi_hasil);
        $this->assertEquals(11000, $paymentTx->amount);
    }
}
