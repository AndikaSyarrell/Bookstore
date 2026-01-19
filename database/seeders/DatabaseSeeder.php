<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test buyer',
            'email' => 'test.buyer@example.com',
            'password' => bcrypt('password'),
            'role_id' => 3,
            'img'=> null,
            'address'=> '123 maint st',
            'birth_date'=> '1999-10-01',
            'no_telp'=> '17443625',
        ]);
    }
}
