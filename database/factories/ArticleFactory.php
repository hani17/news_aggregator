<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $api_id = Str::random(32);

        return [
            'title' => fake()->sentence,
            'published_at' => fake()->dateTime,
            'url' => fake()->url,
            'image_url' => fake()->imageUrl,
            'category_id' => Category::factory(),
            'source_id' => Source::factory(),
            'hash' => sha1($api_id),
            'api_id' => $api_id,
        ];
    }
}
