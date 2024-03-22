<?php

namespace Database\Factories;

use App\Models\Participant;
use App\Models\Sweepstakes;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    public function definition(): array
    {
        return [
            'sweepstakes_id' => Sweepstakes::factory(),
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
