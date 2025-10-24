<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Debtor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['piutang', 'pembayaran']);
        $amount = $this->faker->randomFloat(2, 50000, 5000000);

        // Untuk pembayaran, generate alokasi bagi hasil dan bagi pokok
        if ($type === 'pembayaran') {
            // Pastikan total alokasi tidak melebihi jumlah pembayaran
            $bagiHasil = $this->faker->randomFloat(2, 0, $amount * 0.8); // Maks 80% dari amount
            $bagiPokok = $this->faker->randomFloat(2, 0, $amount - $bagiHasil); // Sisa dari amount
        } else {
            // Untuk piutang, alokasi bisa null atau 0
            $bagiHasil = $this->faker->randomElement([null, 0]);
            $bagiPokok = $this->faker->randomElement([null, 0]);
        }

        return [
            'debtor_id' => Debtor::inRandomOrder()->first()->id,
            'type' => $type,
            'amount' => $amount,
            'bagi_hasil' => $bagiHasil,
            'bagi_pokok' => $bagiPokok,
            'transaction_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'description' => $this->faker->sentence(),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
