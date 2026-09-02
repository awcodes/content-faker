<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Workbench\Database\Factories\PostFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        PostFactory::new()->count(3)->create();

        PostFactory::new()->undecorated()->create([
            'title' => 'Undecorated Example',
            'slug' => 'undecorated-example',
        ]);
    }
}
