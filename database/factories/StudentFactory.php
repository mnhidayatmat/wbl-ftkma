<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\WblGroup;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'matric_no' => fake()->unique()->numerify('A#######'),
            'programme' => fake()->randomElement([
                'Computer Science',
                'Information Technology',
                'Software Engineering',
                'Data Science',
                'Cybersecurity',
            ]),
            'group_id' => WblGroup::factory(),
            'company_id' => Company::factory(),
        ];
    }
}

