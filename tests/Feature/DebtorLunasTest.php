<?php

namespace Tests\Feature;

use App\Models\Debtor;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DebtorLunasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\User::factory()->create();
    }

    public function test_debtor_with_zero_balance_has_zero_saldo_attributes(): void
    {
        $debtor = Debtor::factory()->create();

        // Create transactions that result in 0 balance
        Transaction::factory()->create([
            'debtor_id' => $debtor->id,
            'type' => 'piutang',
            'amount' => -100,
            'bagi_pokok' => -100,
            'bagi_hasil' => 0,
        ]);

        Transaction::factory()->create([
            'debtor_id' => $debtor->id,
            'type' => 'pembayaran',
            'amount' => 100,
            'bagi_pokok' => 100,
            'bagi_hasil' => 0,
        ]);

        $this->assertEquals('Lunas', $debtor->debtor_status);
        $this->assertEquals(0, $debtor->saldo_pokok);
        $this->assertEquals(0, $debtor->saldo_bagi_hasil);
        $this->assertEquals('Rp 0', $debtor->formatted_saldo_pokok);
        $this->assertEquals('Rp 0', $debtor->formatted_saldo_bagi_hasil);
    }
}
