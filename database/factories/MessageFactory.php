<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentences(fake()->numberBetween(1, 3), true),
            'read' => fake()->boolean(40),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Message $message) {
            if ($message->conversation && $message->conversation->users()->exists()) {
                $userId = $message->conversation->users()->inRandomOrder()->value('user_id');
                if ($userId) {
                    $message->user_id = $userId;
                }
            }
        });
    }
}
