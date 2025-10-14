<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Acción'],
            ['name' => 'Aventura'],
            ['name' => 'Comedia'],
            ['name' => 'Drama'],
            ['name' => 'Ciencia Ficción'],
            ['name' => 'Terror'],
            ['name' => 'Romance'],
            ['name' => 'Animación'],
            ['name' => 'Documental'],
            ['name' => 'Thriller'],
            ['name' => 'Misterio'],
            ['name' => 'Musical'],
            ['name' => 'Fantasía'],
            ['name' => 'Western'],
            ['name' => 'Deportes'],
            ['name' => 'Familiar'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}