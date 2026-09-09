<?php

declare(strict_types=1);

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Workbench\App\Models\Post;

Route::get('/', fn (): Factory | View => view('posts.index', [
    'posts' => Post::query()->orderBy('id')->get(),
]))->name('posts.index');

Route::get('/posts/{post}', fn (Post $post): Factory | View => view('posts.show', [
    'post' => $post,
]))->name('posts.show');
