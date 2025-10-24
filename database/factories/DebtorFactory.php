<?php

namespace Database\Factories;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Illuminate\Database\Eloquent\Factories\Factory;

class DebtorFactory extends Factory
{
    protected $model = Debtor::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'initial_balance' => 0, // Set to 0 because initial balance is converted to titipan
            'initial_balance_type' => $this->faker->randomElement(['pokok', 'bagi_hasil']),
            'joined_at' => $this->faker->date(),
            'category' => $this->faker->randomElement(['internal', 'eksternal']),
        ];
    }

    /**
     * Indicate that the debtor has an initial balance that will be converted to titipan.
     */
    public function withInitialBalance(float $amount, string $type = 'pokok'): static
    {
        return $this->state(fn(array $attributes) => [
            'initial_balance' => $amount,
            'initial_balance_type' => $type,
        ])->afterCreating(function (Debtor $debtor) use ($amount, $type) {
            // Simulate the controller logic: convert initial_balance to titipan
            if ($amount > 0) {
                Titipan::create([
                    'debtor_id' => $debtor->id,
                    'amount' => $amount,
                    'tanggal' => now(),
                    'keterangan' => 'Titipan awal (' . ucfirst($type) . ')',
                    'user_id' => 1, // Assuming user with id 1 exists
                ]);
            }
        });
    }

    /**
     * Indicate that the debtor has a piutang transaction.
     */
    public function withPiutang(float $amount): static
    {
        return $this->afterCreating(function (Debtor $debtor) use ($amount) {
            Transaction::create([
                'debtor_id' => $debtor->id,
                'type' => 'piutang',
                'amount' => $amount,
                'bagi_hasil' => 0,
                'bagi_pokok' => 0,
                'transaction_date' => now(),
                'description' => 'Piutang dari factory',
                'user_id' => 1,
            ]);
        });
    }

    /**
     * Indicate that the debtor has a pembayaran transaction that results in overpayment (lebih bayar).
     */
    public function withOverpayment(float $piutangAmount, float $pembayaranAmount): static
    {
        return $this->afterCreating(function (Debtor $debtor) use ($piutangAmount, $pembayaranAmount) {
            // Create piutang transaction
            Transaction::create([
                'debtor_id' => $debtor->id,
                'type' => 'piutang',
                'amount' => $piutangAmount,
                'bagi_hasil' => 0,
                'bagi_pokok' => 0,
                'transaction_date' => now(),
                'description' => 'Piutang dari factory',
                'user_id' => 1,
            ]);

            // Create pembayaran transaction that is more than piutang
            Transaction::create([
                'debtor_id' => $debtor->id,
                'type' => 'pembayaran',
                'amount' => $pembayaranAmount,
                'bagi_hasil' => 0,
                'bagi_pokok' => 0,
                'transaction_date' => now(),
                'description' => 'Pembayaran dari factory',
                'user_id' => 1,
            ]);

            // Calculate overpayment
            $overpayment = $pembayaranAmount - $piutangAmount;
            if ($overpayment > 0) {
                Titipan::create([
                    'debtor_id' => $debtor->id,
                    'amount' => $overpayment,
                    'tanggal' => now(),
                    'keterangan' => 'Kelebihan pembayaran dari factory',
                    'user_id' => 1,
                ]);
            }
        });
    }
}
