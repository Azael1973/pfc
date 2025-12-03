<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Listing;
use App\Models\User;
use App\Models\Category;
use App\Models\ListingImage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = [
            ['name' => 'Madrid', 'lat' => 40.4168, 'lng' => -3.7038],
            ['name' => 'Barcelona', 'lat' => 41.3874, 'lng' => 2.1686],
            ['name' => 'Valencia', 'lat' => 39.4699, 'lng' => -0.3763],
            ['name' => 'Sevilla', 'lat' => 37.3891, 'lng' => -5.9845],
            ['name' => 'Zaragoza', 'lat' => 41.6488, 'lng' => -0.8891],
            ['name' => 'Málaga', 'lat' => 36.7213, 'lng' => -4.4213],
            ['name' => 'Bilbao', 'lat' => 43.2630, 'lng' => -2.9350],
            ['name' => 'Alicante', 'lat' => 38.3452, 'lng' => -0.4810],
            ['name' => 'Murcia', 'lat' => 37.9922, 'lng' => -1.1307],
            ['name' => 'Valladolid', 'lat' => 41.6523, 'lng' => -4.7245],
        ];

        $city = fake()->randomElement($cities);

        $products = [
            'iPhone 12 128GB', 'Samsung Galaxy S21', 'Xiaomi Mi Vacuum', 'PlayStation 5',
            'Xbox Series S', 'Nintendo Switch OLED', 'Portátil HP 15"', 'MacBook Air M1',
            'Bicicleta de montaña', 'Patinete eléctrico Xiaomi', 'Silla gaming', 'Mesa de comedor',
            'Cámara Canon EOS 2000D', 'Guitarra acústica Fender', 'Chaqueta de cuero', 'Zapatillas deportivas',
            'Sofá 3 plazas', 'Lámpara de pie', 'Monitor 27" 144Hz', 'Auriculares Sony WH-1000XM4',
        ];

        $conditions = ['new', 'like_new', 'used', 'for_parts'];
        $statuses = ['available', 'reserved', 'sold'];

        $price = fake()->randomFloat(2, 5, 2000);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->randomElement($products),
            'description' => fake()->paragraphs(asText: true),
            'price' => $price,
            'condition' => fake()->randomElement($conditions),
            'status' => fake()->randomElement($statuses),
            'city' => $city['name'],
            'lat' => $city['lat'] + fake()->randomFloat(6, -0.02, 0.02),
            'lng' => $city['lng'] + fake()->randomFloat(6, -0.02, 0.02),
            'views' => fake()->numberBetween(0, 5000),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Listing $listing) {
            ListingImage::factory()->count(fake()->numberBetween(1, 5))
                ->for($listing)
                ->sequence(fn ($sequence) => ['order' => $sequence->index])
                ->create();
        });
    }

    public function available(): static
    {
        return $this->state(fn () => ['status' => 'available']);
    }

    public function reserved(): static
    {
        return $this->state(fn () => ['status' => 'reserved']);
    }

    public function sold(): static
    {
        return $this->state(fn () => ['status' => 'sold']);
    }
}
