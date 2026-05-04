<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Image;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $agents = User::where('role', 'agent')->get();
        $categories = Category::all();

        $properties = [
            [
                'title' => 'Уютная квартира в центре Бишкека',
                'description' => 'Просторная 2-комнатная квартира с современным ремонтом. Рядом парк, магазины, школа.',
                'price' => 85000,
                'address' => 'ул. Чуй 123',
                'city' => 'Бишкек',
                'rooms' => 2,
                'area' => 65.5,
                'deal_type' => 'sale',
                'status' => 'active',
                'image' => 'images/example-house.jpg',
            ],
            [
                'title' => 'Офис в бизнес-центре',
                'description' => 'Современный офис на 3 этаже. Панорамные окна, парковка, охрана.',
                'price' => 1500,
                'address' => 'пр. Манаса 45',
                'city' => 'Бишкек',
                'rooms' => null,
                'area' => 120.0,
                'deal_type' => 'rent',
                'status' => 'active',
                'image' => 'images/example-house2.jpg',
            ],
            [
                'title' => 'Дом с садом в Оше',
                'description' => 'Просторный дом с большим садом. Тихий район, все коммуникации.',
                'price' => 120000,
                'address' => 'ул. Навои 78',
                'city' => 'Ош',
                'rooms' => 5,
                'area' => 180.0,
                'deal_type' => 'sale',
                'status' => 'active',
                'image' => 'images/example-house3.jpg',
            ],
            [
                'title' => 'Однокомнатная квартира посуточно',
                'description' => 'Чистая квартира с Wi-Fi, стиральной машиной, всей мебелью.',
                'price' => 2500,
                'address' => 'ул. Токтогула 56',
                'city' => 'Бишкек',
                'rooms' => 1,
                'area' => 40.0,
                'deal_type' => 'rent',
                'status' => 'active',
                'image' => 'images/example-house.jpg',
            ],
            [
                'title' => 'Земельный участок под застройку',
                'description' => '10 соток в черте города. Все документы готовы.',
                'price' => 45000,
                'address' => 'мкр Джал',
                'city' => 'Бишкек',
                'rooms' => null,
                'area' => 1000.0,
                'deal_type' => 'sale',
                'status' => 'active',
                'image' => 'images/example-house2.jpg',
            ],
        ];

        foreach ($properties as $index => $data) {
            $agent = $agents[$index % $agents->count()];
            $category = $categories[$index % $categories->count()];

            $image = $data['image'];
            unset($data['image']);

            $property = Property::create([
                ...$data,
                'user_id' => $agent->id,
                'category_id' => $category->id,
            ]);

            Image::create([
                'property_id' => $property->id,
                'path' => $image,
                'is_main' => true,
            ]);
        }
    }
}