<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationAndBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // locations list
        $locations = [
            'Уилмингтон (Северная Каролина)',
            'Портленд (Орегон)',
            'Торонто',
            'Варшава',
            'Валенсия',
            'Шанхай',
        ];

        // foreach locations list name create a location
        // and create blocks, calculate blocks number and volume
        foreach ($locations as $item){
            // create location
            $location = Location::create([
                'name' => $item
            ]);

            // creating random number of blocks for this location
            Block::factory(150)->create([
                'location_id' => $location->id
            ]);

            // save blocks and volume to location
            $location->blocks = 150;
            $location->volume = 150 * 2;
            $location->save();
        }
    }
}
