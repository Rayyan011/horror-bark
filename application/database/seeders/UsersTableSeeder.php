<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'test@admin',
                'email' => 'test@admin.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$GwQ3Ievi3fI1BS3.JYZmeuaXE6lJh3QD/noGYpYDFySEJMoDEwytW',
                'remember_token' => NULL,
                'created_at' => '2025-02-17 21:57:48',
                'updated_at' => '2025-02-17 21:57:48',
            ),
        ));
        
        
    }
}