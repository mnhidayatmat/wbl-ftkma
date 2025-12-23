<?php

namespace Database\Factories;

use App\Models\WblGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WblGroup>
 */
class WblGroupFactory extends Factory
{
    protected $model = WblGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 year', 'now');
        $endDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'name' => fake()->randomElement([
                'Group A',
                'Group B',
                'Group C',
                'Group D',
                'Group E',
            ]) . ' - ' . fake()->year(),
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }
}

