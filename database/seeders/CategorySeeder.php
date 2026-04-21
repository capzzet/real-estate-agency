<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Квартира', 'slug' => 'kvartira'],
            ['name' => 'Дом', 'slug' => 'dom'],
            ['name' => 'Офис', 'slug' => 'ofis'],
            ['name' => 'Участок', 'slug' => 'uchastok'],
            ['name' => 'Коммерческая', 'slug' => 'kommercheskaya'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
