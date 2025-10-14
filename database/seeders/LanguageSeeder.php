<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['name' => 'Inglés', 'code' => 'en'],
            ['name' => 'Español', 'code' => 'es'],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}