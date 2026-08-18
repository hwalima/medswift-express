<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'Admin User',
                'email'             => 'admin@medswift.test',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Test Client',
                'email'             => 'client@medswift.test',
                'password'          => Hash::make('password'),
                'role'              => 'client',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Test Courier',
                'email'             => 'courier@medswift.test',
                'password'          => Hash::make('password'),
                'role'              => 'courier',
                'is_active'         => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info('Test users seeded successfully.');
    }
}
