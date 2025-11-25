<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory()->create(),
            'slug' => $this->faker->slug,
            'vimeo_id' => $this->faker->uuid,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'duration_in_min' => $this->faker->numberBetween(1,99),
        ];
    }
}
