<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

it('has courses', function () {
    // Arrange
    $user = User::factory()
                ->has(Course::factory()->count(2))
                ->create();
    // Act & Assert
    expect($user->courses)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Course::class);
});
