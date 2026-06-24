<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Period::create([
            'name' => '2025',
            'is_active' => true,
        ]);
    }
}
