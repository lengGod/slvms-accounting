<?php

namespace Database\Seeders;

use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NewDebtorTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->call(UserSeeder::class);
            $users = User::all();
        }

        $userId = $users->first()->id;

        $umkmNames = [
            'Toko Kelontong Berkah', 'CV Maju Jaya', 'Warung Makan Sedap Malam',
            'Bengkel Motor Speed', 'Catering Ibu Hajah', 'Toko Bangunan Sumber Makmur',
            'PT Selaras Alam', 'Laundry Wangi Kilat', 'Fotocopy Sahabat',
            'Kedai Kopi Mantap', 'Apotek Sehat Sejahtera', 'Toko Pakaian Mode Baru',
            'Salon Cantik Pesona', 'Percetakan Grafika Jaya', 'Toko Elektronik Sinar',
            'Warung Sembako Barokah', 'CV Kreatif Muda', 'PT Global Solusi',
            'Toko Sepatu Langkah', 'Bakery Manis Legit'
        ];

        shuffle($umkmNames);

        for ($i = 0; $i < 20; $i++) {
            $name = $umkmNames[$i] ?? 'UMKM ' . ($i + 1);
            
            $debtor = Debtor::factory()->create([
                'name' => $name,
                'address' => 'Jl. Merdeka No. ' . ($i + 1) . ', Kota Contoh',
            ]);
            
            if ($i < 7) {
                $this->createPiutangData($debtor, $userId);
            } elseif ($i < 14) {
                $this->createLunasData($debtor, $userId);
            } else {
                $this->createTitipanData($debtor, $userId);
            }
        }
    }

    private function createPiutangData($debtor, $userId)
    {
        $piutangAmount = rand(2000000, 5000000);
        $startDate = Carbon::now()->subMonths(3);
        
        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'piutang',
            'amount' => $piutangAmount,
            'bagi_hasil' => -$piutangAmount * 0.1,
            'bagi_pokok' => -$piutangAmount,
            'transaction_date' => $startDate,
            'description' => 'Pinjaman modal usaha',
            'user_id' => $userId,
        ]);

        $paymentAmount = $piutangAmount * 0.4;
        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'pembayaran',
            'amount' => $paymentAmount,
            'bagi_hasil' => ($piutangAmount * 0.1) * 0.4,
            'bagi_pokok' => $piutangAmount * 0.4,
            'transaction_date' => Carbon::now()->subMonth(),
            'description' => 'Pembayaran angsuran 1',
            'user_id' => $userId,
        ]);
    }

    private function createLunasData($debtor, $userId)
    {
        $piutangAmount = rand(1000000, 3000000);
        $startDate = Carbon::now()->subMonths(4);

        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'piutang',
            'amount' => $piutangAmount,
            'bagi_hasil' => -$piutangAmount * 0.1,
            'bagi_pokok' => -$piutangAmount,
            'transaction_date' => $startDate,
            'description' => 'Pinjaman modal',
            'user_id' => $userId,
        ]);

        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'pembayaran',
            'amount' => $piutangAmount * 1.1,
            'bagi_hasil' => $piutangAmount * 0.1,
            'bagi_pokok' => $piutangAmount,
            'transaction_date' => Carbon::now()->subMonth(),
            'description' => 'Pelunasan pinjaman',
            'user_id' => $userId,
        ]);
    }

    private function createTitipanData($debtor, $userId)
    {
        $titipanAmount = rand(500000, 2000000);
        
        Titipan::create([
            'debtor_id' => $debtor->id,
            'amount' => $titipanAmount,
            'bagi_pokok' => $titipanAmount,
            'bagi_hasil' => 0,
            'tanggal' => Carbon::now(),
            'keterangan' => 'Titipan dana simpanan',
            'user_id' => $userId,
        ]);
    }
}
