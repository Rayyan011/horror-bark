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
        
        \DB::table('hotels')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'The shining my nigga',
                'location' => 'Main island ',
                'created_at' => '2025-03-18 12:10:21',
                'updated_at' => '2025-03-18 12:10:21',
            ),
        ));
        
        
    }
}