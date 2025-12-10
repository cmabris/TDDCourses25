<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'paddle_product_id' => $this->faker->uuid,
            'slug' => $this->faker->slug,
            'tagline' => $this->faker->sentence,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'image_name' => 'image.png',
            'learnings' => ['Learn A', 'Learn B', 'Learn C'],
        ];
    }

    public function released(?Carbon $date = null)
    {
        return $this->state(
            fn (array $attributes) => ['released_at' => $date ?? Carbon::now()]
        );
    }
}
