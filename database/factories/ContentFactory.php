<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'content_type_id' => ContentType::factory(),
            'title' => $title,
            'slug' => str($title)->slug(),
            'excerpt' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'published_at' => $this->faker->dateTime(),
            'metadata_json' => [],
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Content $content) {
            $content->seo()->create([
                'title' => $this->faker->sentence(6),
                'description' => $this->faker->sentence(),
                'canonical_url' => null,
            ]);
        });
    }
}
