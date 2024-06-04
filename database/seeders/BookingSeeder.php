<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // for every location
        foreach (Location::all() as $location){

            // create dates
            $date_start = Carbon::now();
            $date_end = Carbon::now()->addDay();

            // get only ids
            $blocks = $location->blocks()->pluck('id');

            for ($i = 0; $i < 150; ++$i){
                // book the blocks
                $booking = Booking::create([
                    'location_id' => $location->id,
                    'user_id' => rand(1, 10),
                    'temperature' => rand(1, 15),
                    'blocks' => 150, // all blocks
                    'volume' => 300,
                    'date_start' => $date_start->format('Y-m-d'),
                    'date_end' => $date_end->format('Y-m-d'),
                    'price' => 5000,
                    'access_code' => 'HelloThere!',
                ]);

                // sync booking with blocks
                $booking->blocks()->sync($blocks);

                // update dates
                $date_start->addDays(2);
                $date_end->addDays(2);
            }
        }
    }
}
