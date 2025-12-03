<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Listing;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingImage>
 */
class ListingImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => Listing::factory(),
            'path' => 'listings/'.Str::uuid().'.jpg',
            'thumb_path' => 'listings/thumbs/'.Str::uuid().'.jpg',
            'order' => fake()->numberBetween(0, 5),
        ];
    }
}
