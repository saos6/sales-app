<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = $this->faker->randomElement(CategoryType::cases());
        $taxRate = $this->faker->randomElement(['standard', 'reduced', 'exempt']);
        $currency = $this->faker->randomElement(['JPY', 'USD', 'EUR']);

        return [
            'item_code' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}'),
            'item_name' => $this->faker->word . ' ' . $this->faker->word,
            'spec' => $this->faker->sentence(3),
            'category_id' => $category,
            'unit' => $this->faker->randomElement(['個', '台', 'セット', '本']),
            'standard_price' => $this->faker->numberBetween(1000, 1000000),
            'cost_price' => $this->faker->numberBetween(500, 800000),
            'tax_rate' => $taxRate,
            'currency' => $currency,
            'maker' => $this->faker->company,
            'jan_code' => $this->faker->unique()->ean13,
            'stock_qty' => $this->faker->numberBetween(0, 200),
            'reorder_point' => $this->faker->numberBetween(5, 50),
            'lead_time' => $this->faker->numberBetween(1, 30),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'remarks' => $this->faker->sentence,
        ];
    }
}
