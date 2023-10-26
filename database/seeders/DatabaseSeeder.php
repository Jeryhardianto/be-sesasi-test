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
        \App\Models\User::factory(1)->create();
        // create seeder for user
        \App\Models\User::factory(1)->create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => 'password',
            'role' => 'user',
        ]);
        \App\Models\User::factory(1)->create([
            'name' => 'Editor',
            'email' => 'editor@test.com',
            'password' => 'password',
            'role' => 'editor',
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@test.com',
        //     'password' => 'password',
        //     'role' => 'admin',
        // ]);
    }
}
