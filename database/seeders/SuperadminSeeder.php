<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@test.in'],
            [
                'name'                => 'Super Admin',
                'password'            => Hash::make('password'),
                'role'                => UserRole::Superadmin,
                'is_admin'            => true,
                'currency'            => 'INR',
                'onboarding_completed'=> true,
            ]
        );
    }
}
