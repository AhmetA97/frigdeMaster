<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         \App\Models\User::factory(10)->create();

        $this->call(LocationAndBlockSeeder::class);

        // bookings with denormalized table
        // $this->call(BookingSeeder2::class);

        // bookings with normalized table
        // $this->call(BookingSeeder::class);
    }
}
