<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'symbol' => fake()->unique()->regexify('[A-Z]{4}[0-9]{1,2}'),
            'name' => fake()->company(),
            'type' => fake()->randomElement(['FIXED', 'VARIABLE']),
        ];
    }
}
