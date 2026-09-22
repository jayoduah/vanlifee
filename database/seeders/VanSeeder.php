<?php

namespace Database\Seeders;

use App\Models\Van;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $van1 = Van::create([
            'user_id' => 1, // Assuming the first user is the owner
            'make' => 'Mercedes-Benz GLE 350',
            'model' => 'Sprinter 2500000',
            'year' => 2026,
            'price_per_day' => 1200.00,
            'description' => 'Luxury passenger van with leather seating, Apple CarPlay, and high roof.',
        ]);
        $van2 = Van::create([
            'user_id' => 2, // Assuming the second user is the owner
            'make' => 'Mercedes-Benz GLE 350s',
            'model' => 'Sprinter 2500000',
            'year' => 2026,
            'price_per_day' => 1200.00,
            'description' => 'Luxury passenger van with leather seating, Apple CarPlay, and high roof.',
        ]);

        $van1->save();
        $van2->save();
        $this->command->info('VanSeeder completed successfully.');
    }

    

}
