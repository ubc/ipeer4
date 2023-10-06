<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \Datetime;
use \DatetimeInterface;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => fake()->catchPhrase(),
            'desc'         => fake()->realTextBetween(50,100),
            'due'          => fake()->dateTimeInInterval('+1 hour', '+1 week'),
            'open_from'    => fake()->dateTimeInInterval('-1 hour', '-1 week'),
        ];
    }
}
