<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\LedgerAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        Account::firstOrCreate(
            ['email' => 'accountant@inlinecrm.com'],
            [
                'name' => 'Account Admin',
                'mobile' => '9999999999',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 1,
            ]
        );

        LedgerAccount::firstOrCreate(
            ['name' => 'Main Cash'],
            [
                'type' => 'cash',
                'opening_balance' => 0,
                'status' => 'Active',
                'description' => 'Primary cash account',
            ]
        );

        LedgerAccount::firstOrCreate(
            ['name' => 'Main Bank Account'],
            [
                'type' => 'bank',
                'bank_name' => 'State Bank',
                'account_number' => '1234567890',
                'ifsc_code' => 'SBIN0001234',
                'opening_balance' => 0,
                'status' => 'Active',
                'description' => 'Primary bank account',
            ]
        );
    }
}
