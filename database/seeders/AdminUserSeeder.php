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
        // Check if admin exists, if not create
        $user = User::where('email', 'lwin@gmail.com')->first();

        if (!$user) {
            $user = new User();
            $user->name = 'Admin Lwin';
            $user->email = 'lwin@gmail.com';
            $user->password = Hash::make('lwin');
            $user->role = 'admin'; // Explicitly set role
            $user->save();
        } else {
            // Update existing user to admin if needed
            $user->role = 'admin';
            $user->password = Hash::make('069672'); // Reset password to ensure access
            $user->save();
        }
    }
}
