<?php

declare(strict_types=1);

namespace Awcodes\ContentFaker;

use Illuminate\Support\ServiceProvider;
use Override;

class ContentFakerServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/content-faker.php',
            'content-faker'
        );

        $this->app->bind(MarkdownFaker::class, fn (): MarkdownFaker => MarkdownFaker::make());
        $this->app->bind(HtmlFaker::class, fn (): HtmlFaker => HtmlFaker::make());
        $this->app->bind(RichEditorFaker::class, fn (): RichEditorFaker => RichEditorFaker::make());
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/content-faker.php' => config_path('content-faker.php'),
        ], 'content-faker-config');
    }
}
