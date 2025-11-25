<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug,
        ];
    }
}
