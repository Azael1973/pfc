<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Favorite;
use App\Models\Listing;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'listing_id' => Listing::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Favorite $favorite) {
            if ($favorite->listing && $favorite->listing->user_id === $favorite->user_id) {
                $favorite->user()->associate(User::factory()->create());
            }
        });
    }
}
