<?php

namespace Database\Factories;

use App\Models\Moment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Moment>
 */
class MomentFactory extends Factory
{
    protected $model = Moment::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'status' => 'published',
            'published_at' => now(),
            'featured' => false,
            'sort_order' => 0,
            'external_link' => null,
            'external_link_label' => null,
            'people' => [],
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => [
            'featured' => true,
        ]);
    }
}
