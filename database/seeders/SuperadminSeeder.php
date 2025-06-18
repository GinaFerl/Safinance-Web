<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         if(!User::where('email', 'superadmin@gmail.com')->exists()) {
            User::create([
                'username' => 'superadmin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('S@f1nanc3*4CC'),
                'profileImage' => null,
                'role' => 'superadmin',
            ]);
        }

        $this->command->info('Superadmin user seeded!');
    }
}
