<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $superadmin = config('superadmin.default_superadmin');

        $user = User::firstOrCreate(
            ['email' => $superadmin['email']],
            [
                'name'                => $superadmin['name'],
                'mobile'              => $superadmin['mobile'],
                'password'            => Hash::make($superadmin['password']),
                'role'                => UserRole::Superadmin,
                'is_admin'            => true,
                'currency'            => 'INR',
                'onboarding_completed'=> true,
            ]
        );
        Account::create([
            'user_id' => $user->id,
            'name'    => 'Cash',
            'type'    => AccountType::Cash,
            'balance' => 0,
        ]);
    }
}
