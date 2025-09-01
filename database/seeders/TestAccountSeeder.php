<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    public function run()
    {
        // Create test customer company
        $customer = Customer::firstOrCreate(
            ['email' => 'piotr.gesior@gmail.com'],
            [
                'company_name' => 'Test Company Sp. z o.o.',
                'company_short_name' => 'Test Company',
                'nip' => '1234567890',
                'regon' => '123456789',
                'company_address' => 'ul. Testowa 123',
                'city' => 'Warszawa', 
                'postal_code' => '00-001',
                'country' => 'PL',
                'phone' => '+48123456789',
                'website' => 'https://test.company',
                'status' => 'active',
                'credit_limit' => 5000.00,
                'current_balance' => 20000.00, // 20,000 PLN na testy
                'email_verified' => true,
                'verified_at' => now(),
                'contract_signed_at' => now(),
            ]
        );

        // Create primary user for the customer
        CustomerUser::firstOrCreate(
            ['email' => 'piotr.gesior@gmail.com'],
            [
                'customer_id' => $customer->id,
                'first_name' => 'Piotr',
                'last_name' => 'Gęsior',
                'email' => 'piotr.gesior@gmail.com',
                'phone' => '+48123456789',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_primary' => true,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ]
        );

        $this->command->info('✅ Test account created:');
        $this->command->info('   Email: piotr.gesior@gmail.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Balance: 20,000.00 PLN');
        $this->command->info('   Credit Limit: 5,000.00 PLN');
        $this->command->info('   Company: Test Company Sp. z o.o.');
    }
}