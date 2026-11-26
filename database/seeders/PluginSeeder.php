<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Plugin;
use Illuminate\Database\Seeder;

class PluginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plugin::firstOrCreate(
            ['slug' => 'HelloLaraPress'],
            [
                'name' => 'Hello LaraPress',
                'version' => '1.0.0',
                'is_active' => true,
                'settings_json' => [
                    'greeting' => 'Olá! Bem-vindo ao LaraPress CMS.',
                    'enable_notice' => true,
                ],
            ]
        );
    }
}
