<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "register_id" => rand(1,5),
            "updater_id" => null,
            "petitioner_id" => rand(1,5),
            "agent_id" => rand(1,2),
            "type_request_id" => rand(1,2),
            "priority_request_id" => null,
            "satisfaction_request_id" => null,
            "state_request_id" => 1,
            "title" => fake()->sentence(),
            "description" => fake()->text(),
            "tentative_end_date" => fake()->dateTimeBetween('+1 week', '+2 week')


        ];
    }
}
