<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pages')->delete();
        
        \DB::table('pages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'page_name' => 'home',
                'content' => '[]',
                'created_at' => '2025-02-19 11:27:24',
                'updated_at' => '2025-02-19 11:27:24',
            ),
        ));
        
        
    }
}