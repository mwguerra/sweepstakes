<?php

namespace Database\Factories;

use App\Models\Sweepstakes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_name' => $this->faker->word . '.txt',
            'size' => $this->faker->numberBetween(1000, 5000),
            'path' => 'public/' . $this->faker->unique()->sha256 . '.txt',
            'mime_type' => 'text/plain',
            // 'fileable_id' and 'fileable_type' will be set when attaching the File to a model.
        ];
    }
}
