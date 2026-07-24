<?php

namespace Database\Factories;

use App\Models\ContentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContentType>
 */
class ContentTypeFactory extends Factory
{
    protected $model = ContentType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'kind' => 'collection',
            'template' => 'default',
            'is_system' => false,
            'is_active' => true,
        ];
    }
}
