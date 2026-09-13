<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Van;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Van Owners
        $owner1 = User::create([
            'name' => 'John (Van Owner)',
            'email' => 'owner@example.com',
            'phone_number' => '+1-555-0100',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        $owner2 = User::create([
            'name' => 'Alice (Van Owner 2)',
            'email' => 'owner2@example.com',
            'phone_number' => '+1-555-0200',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        // 2. Create Customers
        $customer1 = User::create([
            'name' => 'Bob (Customer)',
            'email' => 'customer@example.com',
            'phone_number' => '+1-555-0300',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $customer2 = User::create([
            'name' => 'Sarah (Customer 2)',
            'email' => 'customer2@example.com',
            'phone_number' => '+1-555-0400',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        // 3. Create Vans for Owner 1
        $van1 = Van::create([
            'user_id' => $owner1->id,
            'make' => 'Ford',
            'model' => 'Transit Custom',
            'year' => 2023,
            'price_per_day' => 85.00,
            'description' => 'Reliable and spacious cargo van, perfect for road trips and moving goods.',
        ]);

        $van2 = Van::create([
            'user_id' => $owner1->id,
            'make' => 'Mercedes-Benz',
            'model' => 'Sprinter 2500',
            'year' => 2024,
            'price_per_day' => 120.00,
            'description' => 'Luxury passenger van with leather seating, Apple CarPlay, and high roof.',
        ]);

        // 4. Create Van for Owner 2
        $van3 = Van::create([
            'user_id' => $owner2->id,
            'make' => 'Volkswagen',
            'model' => 'Transporter T6.1',
            'year' => 2022,
            'price_per_day' => 95.00,
            'description' => 'Compact and fuel-efficient camper van outfitted with fold-out bed.',
        ]);

        // 5. Create Reviews
        Review::create([
            'van_id' => $van1->id,
            'user_id' => $customer1->id,
            'rating' => 5,
            'comment' => 'Fantastic van! Super clean, drove smoothly across state lines.',
        ]);

        Review::create([
            'van_id' => $van1->id,
            'user_id' => $customer2->id,
            'rating' => 4,
            'comment' => 'Very spacious and comfortable. The owner John was very polite and punctual.',
        ]);

        Review::create([
            'van_id' => $van3->id,
            'user_id' => $customer1->id,
            'rating' => 5,
            'comment' => 'Loved the camper setup. Everything worked as described.',
        ]);
    }
}
