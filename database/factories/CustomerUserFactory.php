<?php

namespace Database\Factories;

use App\Models\CustomerUser;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerUserFactory extends Factory
{
    protected $model = CustomerUser::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'customer_id' => Customer::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => $this->faker->phoneNumber(),
            'role' => 'user',
            'is_active' => true,
            'last_login_at' => $this->faker->dateTimeThisMonth(),
        ];
    }
}