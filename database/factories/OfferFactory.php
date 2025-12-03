<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Offer;
use App\Models\Listing;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offer>
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['pending', 'accepted', 'rejected', 'withdrawn'];

        return [
            'listing_id' => Listing::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 5, 2000),
            'status' => fake()->randomElement($statuses),
            'message' => fake()->boolean(70) ? fake()->sentence() : null,
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Offer $offer) {
            if ($offer->listing && $offer->listing->price) {
                $offer->amount = round(max(1, $offer->listing->price * fake()->randomFloat(2, 0.6, 1.05)), 2);
            }

            if ($offer->listing && $offer->user_id == $offer->listing->user_id) {
                $buyer = User::factory()->create();
                $offer->user()->associate($buyer);
            }
        });
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }

    public function withdrawn(): static
    {
        return $this->state(fn () => ['status' => 'withdrawn']);
    }
}
