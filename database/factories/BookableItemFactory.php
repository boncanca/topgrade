<?php

namespace Database\Factories;

use App\Models\BookableItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookableItem>
 */
class BookableItemFactory extends Factory
{
    protected $model = BookableItem::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->sentence(),
            'duration_minutes' => $this->faker->numberBetween(30, 120),
            'location' => $this->faker->city(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'currency' => 'GBP',
            'capacity' => $this->faker->numberBetween(5, 20),
            'booking_label' => 'Book Now',
            'is_active' => true,
            'requires_payment' => false,
        ];
    }
}
