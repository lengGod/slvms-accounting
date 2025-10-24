<?php

namespace Database\Seeders;

use App\Models\Debtor;
use Illuminate\Database\Seeder;

class DebtorSeeder extends Seeder
{
    public function run(): void
    {
        Debtor::factory()->count(10)->create();
    }
}
