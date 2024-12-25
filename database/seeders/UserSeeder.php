<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\RolesEnum;
use Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => '12345678',

        ])->assignRole(RolesEnum::User->value);

        User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor@example.com',
            'password' => '12345678',

        ])->assignRole(RolesEnum::Vendor->value);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => '12345678',

        ])->assignRole(RolesEnum::Admin->value);
    }
}
