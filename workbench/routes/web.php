<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Post;

Route::get('/', fn () => view('posts.index', [
    'posts' => Post::query()->orderBy('id')->get(),
]))->name('posts.index');

Route::get('/posts/{post}', fn (Post $post) => view('posts.show', [
    'post' => $post,
]))->name('posts.show');
