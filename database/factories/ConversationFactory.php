<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Conversation;
use App\Models\Listing;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => fake()->boolean(80) ? Listing::factory() : null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Conversation $conversation) {
            $participants = [];

            if ($conversation->listing) {
                $ownerId = $conversation->listing->user_id;
                $participants[] = $ownerId;
                $buyer = User::factory()->create();
                if ($buyer->id !== $ownerId) {
                    $participants[] = $buyer->id;
                }
            } else {
                $u1 = User::factory()->create();
                $u2 = User::factory()->create();
                $participants = [$u1->id, $u2->id];
            }

            $participants = array_values(array_unique($participants));
            if (count($participants) < 2) {
                $participants[] = User::factory()->create()->id;
            }

            $conversation->users()->sync($participants);
        });
    }
}
