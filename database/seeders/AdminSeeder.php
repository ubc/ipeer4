<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\User;


class AdminSeeder extends Seeder
{
    /**
     * Create a default admin user if it doesn't exist
     */
    public function run(): void
    {
        $tellRandomPassword = false;
        $username = config('ipeer.defaultAdminUser.username');
        $password = config('ipeer.defaultAdminUser.password');
        if (!$password) {
            Log::notice('Default admin user password empty, generating a random one');
            $password = Str::random(20);
            $tellRandomPassword = true;
        }
        $admin = User::create([
            'username' => $username,
            'name' => 'Default Admin',
            'password' => $password,
        ]);
        $admin->assignRole('admin');
        if ($tellRandomPassword) {
            Log::notice('Default admin user credentials:');
            Log::notice("  Username: $username");
            Log::notice("  Password: $password");
        }
        else {
            Log::notice('Default admin user created.');
        }
    }
}
