<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Debtor;
use App\Models\Transaction;
use App\Models\Titipan;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ComplexWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin & Staff
        $admin = User::updateOrCreate(
            ['email' => 'admin@slv.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@slv.com'],
            [
                'name' => 'Staff Accounting',
                'password' => Hash::make('password'),
            ]
        );

        $users = [$admin->id, $staff->id];

        // Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Transaction::truncate();
        Titipan::truncate();
        Debtor::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $bizNames = ['Barokah', 'Maju Terus', 'Sinar Terang', 'Sumber Rejeki', 'Lancar Jaya', 'Abadi', 'Sentosa', 'Mandiri', 'Utama', 'Sejahtera', 'Indah', 'Kencana', 'Mulya', 'Prima', 'Global', 'Nusantara'];
        $locations = ['Pusat', 'Cabang Utama', 'Wilayah Selatan', 'Kawasan Industri', 'Pasar Baru', 'KM 12', 'Sektor 5', 'Dermaga'];

        for ($i = 1; $i <= 100; $i++) {
            $category = ['Retail', 'Grosir', 'Instansi', 'Jasa'][rand(0, 3)];
            
            if ($i <= 40) $scenario = 'piutang';
            elseif ($i <= 70) $scenario = 'lunas';
            else $scenario = 'titipan';

            $name = $this->generateBizName($category, $bizNames, $locations);

            // Tanggal bergabung (6-12 bulan lalu)
            $joinedDate = Carbon::now()->subMonths(rand(6, 12))->subDays(rand(1, 28));

            $debtor = Debtor::create([
                'name' => $name,
                'code' => "DBT-" . str_pad($i, 3, '0', STR_PAD_LEFT),
                'address' => "Kawasan Bisnis No. " . rand(1, 500) . ", Blok " . chr(rand(65, 90)),
                'phone' => "021-" . rand(1000000, 9999999),
                'joined_at' => $joinedDate,
                'category' => $category,
                'initial_balance' => 0,
                'initial_balance_type' => 'pokok',
                'initial_pokok_balance' => 0,
                'initial_bagi_hasil_balance' => 0,
            ]);

            // Tanggal transaksi utama (acak antara joined_at sampai 3 bulan lalu)
            $trxDate = (clone $joinedDate)->addMonths(rand(1, 3))->addDays(rand(1, 20));

            if ($scenario == 'piutang') {
                $pokok = rand(2000000, 8000000);
                $this->createPiutang($debtor, $pokok, $trxDate, $users);
                
                // Bayar cicilan 15-30 hari kemudian
                $payDate = (clone $trxDate)->addDays(rand(15, 30));
                $this->createPembayaran($debtor, $pokok * 0.3, 0, $payDate, $users);
            } 
            elseif ($scenario == 'lunas') {
                $pokok = rand(1000000, 5000000);
                $bagiHasil = $pokok * 0.1;
                $this->createPiutang($debtor, $pokok, $trxDate, $users);
                
                // Lunas dalam 2 tahap pembayaran
                $payDate1 = (clone $trxDate)->addDays(rand(10, 20));
                $this->createPembayaran($debtor, $pokok * 0.5, $bagiHasil * 0.5, $payDate1, $users);
                
                $payDate2 = (clone $payDate1)->addDays(rand(15, 25));
                $this->createPembayaran($debtor, $pokok * 0.5, $bagiHasil * 0.5, $payDate2, $users);
            } 
            else { // Titipan
                $pokok = rand(1000000, 3000000);
                $bagiHasil = $pokok * 0.1;
                $this->createPiutang($debtor, $pokok, $trxDate, $users);
                
                // Bayar lunas dulu
                $payDate = (clone $trxDate)->addDays(rand(10, 30));
                $this->createPembayaran($debtor, $pokok, $bagiHasil, $payDate, $users); 
                
                // Baru ada titipan masuk 1 minggu kemudian
                $titipDate = (clone $payDate)->addDays(rand(7, 14));
                $titipAmount = rand(500000, 2000000);
                Titipan::create([
                    'debtor_id' => $debtor->id,
                    'amount' => $titipAmount,
                    'bagi_pokok' => $titipAmount,
                    'bagi_hasil' => 0,
                    'tanggal' => $titipDate,
                    'keterangan' => "Dana Titipan Aktif",
                    'user_id' => $users[array_rand($users)],
                ]);
            }
        }

        $this->command->info('Seeder Selesai: 100 Nama Bisnis & Tanggal Realistis telah dibuat!');
    }

    private function generateBizName($category, $bizNames, $locations)
    {
        $bizBase = $bizNames[array_rand($bizNames)];
        if ($category == 'Retail') {
            $prefix = ['Toko ', 'Kios ', 'Minimarket ', 'Bengkel ', 'Apotek '];
            return $prefix[array_rand($prefix)] . $bizBase;
        } elseif ($category == 'Grosir') {
            $prefix = ['PT ', 'CV ', 'UD ', 'Pabrik ', 'Gudang '];
            return $prefix[array_rand($prefix)] . $bizBase . ' ' . $locations[array_rand($locations)];
        } elseif ($category == 'Instansi') {
            $prefix = ['Koperasi ', 'Yayasan ', 'Lembaga ', 'Dinas ', 'Kantor '];
            return $prefix[array_rand($prefix)] . $bizBase;
        } else {
            $prefix = ['Resto ', 'Cafe ', 'Hotel ', 'Logistik ', 'Cargo '];
            return $prefix[array_rand($prefix)] . $bizBase;
        }
    }

    private function createPiutang($debtor, $pokok, $date, $users)
    {
        $bagiHasil = $pokok * 0.1;
        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'piutang',
            'amount' => -($pokok + $bagiHasil),
            'bagi_hasil' => -$bagiHasil,
            'bagi_pokok' => -$pokok,
            'transaction_date' => $date,
            'description' => "Invoice #" . rand(1000, 9999) . " - Pengadaan Operasional",
            'user_id' => $users[array_rand($users)],
        ]);
    }

    private function createPembayaran($debtor, $pokok, $bagiHasil, $date, $users)
    {
        Transaction::create([
            'debtor_id' => $debtor->id,
            'type' => 'pembayaran',
            'amount' => ($pokok + $bagiHasil),
            'bagi_hasil' => $bagiHasil,
            'bagi_pokok' => $pokok,
            'transaction_date' => $date,
            'description' => "Pembayaran Invoice via Transfer",
            'user_id' => $users[array_rand($users)],
        ]);
    }
}
