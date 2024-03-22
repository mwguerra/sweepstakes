<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\Sweepstakes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SweepstakesFactory extends Factory
{
    protected $model = Sweepstakes::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => Str::slug($this->faker->unique()->sentence),
            'description' => $this->faker->paragraph,
            'winner_email_message' => $this->faker->paragraph,
            'draw_time' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'timezone' => $this->faker->timezone,
            'is_over' => $this->faker->boolean,
            'winner_id' => null,
            'is_winner_notified' => $this->faker->boolean,
        ];
    }


}
