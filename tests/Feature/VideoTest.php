<?php

use App\Models\User;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('attaches multiple categories to a video', function () {
    $categories = Category::factory(3)->create();

    $video = Video::factory()->create();

    $video->categories()->attach($categories->random(2)->pluck('id'));

    $video->load('categories');

    expect($video->categories)->toHaveCount(2);

    expect($video->categories->first())->toBeInstanceOf(Category::class);
});

it('creates a video and assign to a user', function () {
    $user = User::factory()->create();

    $categories = Category::factory(3)->create();

    $video = Video::factory()->create([
        'user_id' => $user->id,
    ]);

    $video->categories()->attach(
        $categories->pluck('id')->random(2)
    );

    expect($video->user)->toBeInstanceOf(User::class);

    expect($video->categories)->toHaveCount(2);
    expect($video->categories->first())->toBeInstanceOf(Category::class);
});
