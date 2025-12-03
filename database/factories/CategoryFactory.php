<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $names = [
            'Electrónica',
            'Móviles y Telefonía',
            'Informática',
            'Hogar y Jardín',
            'Deportes y Ocio',
            'Moda y Accesorios',
            'Motor',
            'Bebés y Niños',
            'Coleccionismo',
            'Libros y Música',
            'Videojuegos',
            'Cine y Series',
            'Mascotas',
            'Cámara y Fotografía',
            'Instrumentos Musicales',
        ];

        $name = fake()->unique()->randomElement($names);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
