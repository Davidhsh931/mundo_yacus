<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\GuineaPig;
use App\Models\GuineaPigImage;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Categoría
        $cat = Category::create([
            'name' => 'Cuy Peruano',
            'description' => 'Cuyes de raza pura'
        ]);

        // 2. Crear un Cuy
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

        // 3. Agregarle una imagen (Asegúrate que el archivo exista en public/images)
        GuineaPigImage::create([
            'guinea_pig_id' => $pig->id,
            'image_path' => '/images/images.jpeg'
        ]);
    }
}