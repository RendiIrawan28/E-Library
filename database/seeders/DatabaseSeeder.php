<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::insert([
            [
                'name' => 'test1',
                'email' => 'test@gmail.com',
                'password' => bcrypt('asdasdasd'),
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
            [
                'name' => 'test2',
                'email' => 'test2@gmail.com',
                'password' => bcrypt('asdasdasd'),
                'email_verified_at' => now(),
                'role' => 'siswa',
            ],
            ]);
    }
}
