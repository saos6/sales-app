<?php

namespace Database\Factories;

use App\Enums\CustomerRank;
use App\Enums\CustomerType;
use App\Enums\IndustryType;
use App\Enums\PaymentTerms;
use App\Models\Dept;
use App\Models\Emp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cust>
 */
class CustFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'name_kana' => $this->faker->company . 'カナ',
            'department_name' => $this->faker->jobTitle,
            'contact_person_name' => $this->faker->name,
            'postal_code' => $this->faker->postcode,
            'prefecture' => $this->faker->prefecture,
            'address_line' => $this->faker->address,
            'tel' => $this->faker->phoneNumber,
            'fax' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'website_url' => $this->faker->url,
            'customer_type' => $this->faker->randomElement(CustomerType::cases()),
            'industry_type' => $this->faker->randomElement(IndustryType::cases()),
            'first_trade_date' => $this->faker->date(),
            'customer_rank' => $this->faker->randomElement(CustomerRank::cases()),
            'payment_terms' => $this->faker->randomElement(PaymentTerms::cases()),
            'depts_id' => Dept::factory(),
            'emps_id' => Emp::factory(),
            'remarks' => $this->faker->realText(),
        ];
    }
}