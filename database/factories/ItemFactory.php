<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
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
        return [
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'category' => $this->faker->randomElement(['Eletrônicos', 'Alimentos', 'Móveis', 'Livros', 'Brinquedos', 'Ferramentas', 'Outros']),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
