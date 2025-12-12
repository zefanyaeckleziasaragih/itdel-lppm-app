<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Penghargaan;

class PenghargaanSeeder extends Seeder
{
    public function run(): void
    {
        Penghargaan::factory()->count(20)->create();
    }
}
