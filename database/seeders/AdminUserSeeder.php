<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'omeshdewangan@gmail.com'],
            [
                'name' => 'Omesh Dewangan',
                'password' => Hash::make('passward123'),
            ]
        );

        // Assign super_admin role directly to this user
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('super_admin');
        }
    }
}
