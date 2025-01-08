<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\RolesEnum;
use App\VendorStatusEnum;
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
            'email' => 'user1@example.com',
            'password' => '12345678',

        ])->assignRole(RolesEnum::User->value);

        $user = User::factory()->create([
            'name' => 'Vendor',
            'email' => 'vendor1@example.com',
            'password' => '12345678',

        ]);
        $user->assignRole(RolesEnum::Vendor->value);

        Vendor::factory()->create([
            'user_id' => $user->id,
            'status' => VendorStatusEnum::Approved,
            'store_name' => 'Vendor Store',
            'store_address' => fake()->address(),
            
        ])

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin1@example.com',
            'password' => '12345678',

        ])->assignRole(RolesEnum::Admin->value);
    }
}
