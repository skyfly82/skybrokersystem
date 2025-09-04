<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'company_name' => $this->faker->company(),
            'nip' => $this->faker->numerify('##########'),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country' => 'PL',
            'current_balance' => $this->faker->randomFloat(2, 0, 1000),
            'credit_limit' => $this->faker->randomFloat(2, 0, 500),
            'is_active' => true,
            'api_key' => 'sk_'.Str::random(46),
            'payment_terms' => 30,
        ];
    }
}
