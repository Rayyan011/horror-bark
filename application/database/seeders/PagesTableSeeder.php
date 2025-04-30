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
                'content' => '[{"data": {"pos": "hero_image", "content": "01JMF11YKY2EYCPWYYNKCQH493.webp"}, "type": "image"}]',
                'created_at' => '2025-02-19 11:27:24',
                'updated_at' => '2025-02-19 11:59:04',
            ),
            1 => 
            array (
                'id' => 2,
                'page_name' => 'about',
                'content' => '[]',
                'created_at' => '2025-04-30 10:54:09',
                'updated_at' => '2025-04-30 10:54:09',
            ),
        ));
        
        
    }
}