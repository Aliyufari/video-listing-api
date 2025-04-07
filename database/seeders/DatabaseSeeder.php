<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Video;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create categories
        $categories = Category::factory(3)->create();

        // Create 3 users
        $users = User::factory(3)->create();

        // Create 5 videos and assign random users & categories
        Video::factory(5)
            ->recycle($users)
            ->create()
            ->each(
                fn($video) =>
                $video->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')
                )
            );
    }
}
