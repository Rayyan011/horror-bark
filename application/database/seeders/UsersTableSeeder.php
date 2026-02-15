<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'test@admin.com';
        $now = now();

        DB::table('users')->updateOrInsert(
            ['email' => $email],
            [
                'name' => 'test@admin',
                'email_verified_at' => null,
                'password' => Hash::make('test@admin.com'),
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
