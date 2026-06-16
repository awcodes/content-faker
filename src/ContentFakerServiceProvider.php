<?php

namespace Awcodes\ContentFaker;

use Illuminate\Support\ServiceProvider;

class ContentFakerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/content-faker.php',
            'content-faker'
        );

        $this->app->bind(MarkdownFaker::class, fn () => MarkdownFaker::make());
        $this->app->bind(HtmlFaker::class, fn () => HtmlFaker::make());
        $this->app->bind(RichEditorFaker::class, fn () => RichEditorFaker::make());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/content-faker.php' => config_path('content-faker.php'),
        ], 'content-faker-config');
    }
}
