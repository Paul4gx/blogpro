<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@blogpro.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@blogpro.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create author user
        User::firstOrCreate(
            ['email' => 'author@blogpro.com'],
            [
                'name' => 'Author User',
                'email' => 'author@blogpro.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and Author users created successfully!');
        $this->command->info('Admin Email: admin@blogpro.com');
        $this->command->info('Author Email: author@blogpro.com');
        $this->command->warn('Default Password: password (Please change after first login!)');
    }
}



