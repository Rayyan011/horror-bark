<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HotelsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('hotels')->delete();

        \DB::table('hotels')->insert([
            0 => [
                'id' => 1,
                'user_id' => 1,
                'name' => 'The Shining Manor',
                'location' => 'Main island ',
                'created_at' => '2025-03-18 12:10:21',
                'updated_at' => '2025-03-18 12:10:21',
            ],
        ]);

    }
}
