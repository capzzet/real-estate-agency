<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'name' => 'Алексей Иванов',
                'email' => 'alexey@estate.com',
                'password' => Hash::make('password'),
                'phone' => '+996 700 123 456',
                'role' => 'agent',
            ],
            [
                'name' => 'Мария Петрова',
                'email' => 'maria@estate.com',
                'password' => Hash::make('password'),
                'phone' => '+996 555 234 567',
                'role' => 'agent',
            ],
            [
                'name' => 'Дмитрий Сидоров',
                'email' => 'dmitry@estate.com',
                'password' => Hash::make('password'),
                'phone' => '+996 700 345 678',
                'role' => 'agent',
            ],
        ];

        foreach ($agents as $agent) {
            User::create($agent);
        }
    }
}
