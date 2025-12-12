<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PenghargaanFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Sesuaikan dengan kolom pada migration: `nama`, `tanggal`, `status`
            'nama'    => $this->faker->sentence(6),
            'tanggal' => $this->faker->date(),
            'status'  => $this->faker->randomElement(['Menunggu', 'Disetujui', 'Ditolak']),
        ];
    }
}
