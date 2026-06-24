<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['name' => 'Content Creator', 'color_hex' => '#3B82F6'],
            ['name' => 'Fotographer & Videographer', 'color_hex' => '#EF4444'],
            ['name' => 'Host', 'color_hex' => '#F59E0B'],
            ['name' => 'Broadcast', 'color_hex' => '#10B981'],
            ['name' => 'Admin', 'color_hex' => '#8B5CF6'],
            ['name' => 'Graphic Designer', 'color_hex' => '#EC4899'],
        ];

        foreach ($divisions as $division) {
            \App\Models\Division::create($division);
        }
    }
}
