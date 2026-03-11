<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\GuineaPig;
use App\Models\GuineaPigImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuario
        User::create([
            'name' => 'David',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        // 2. Crear Categoría
        $cat = Category::create([
            'name' => 'Cuy Peruano',
            'description' => 'Cuyes de raza pura'
        ]);

        // 3. Crear Cuy
        $pig = GuineaPig::create([
            'name' => 'Yacu Real',
            'breed' => 'Americano',
            'average_weight' => 950.00,
            'price' => 55.00,
            'stock' => 15,
            'description' => 'Un ejemplar magnífico para Mundo Yacus',
            'category_id' => $cat->id,
            'active' => true
        ]);

        // 4. Imagen
        GuineaPigImage::create([
            'guinea_pig_id' => $pig->id,
            'image_path' => '/images/images.jpeg',
            'position' => 1
        ]);
    }
}