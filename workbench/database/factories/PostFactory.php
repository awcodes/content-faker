<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Workbench\App\Models\Post;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /** @var class-string<Post> */
    protected $model = Post::class;

    /**
     * The factory mirrors the usage documented in the package README:
     * each body column is filled by one of the three fakers.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::headline(fake()->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'markdown_content' => markdown_faker()
                ->docsPage()
                ->render(),
            'html_content' => html_faker()
                ->article()
                ->render(),
            'rich_content' => rich_editor_faker()
                ->docsPage()
                ->filamentBlock('cta', [
                    'heading' => 'Ready to get started?',
                    'url' => '#',
                ])
                ->render(),
        ];
    }

    /**
     * Content generated with inline decorations turned off, so the
     * effect of the decoration settings can be compared side by side.
     */
    public function undecorated(): static
    {
        return $this->state(fn (): array => [
            'markdown_content' => markdown_faker()
                ->withoutInlineDecorations()
                ->docsPage()
                ->render(),
            'html_content' => html_faker()
                ->withoutInlineDecorations()
                ->article()
                ->render(),
            'rich_content' => rich_editor_faker()
                ->withoutInlineDecorations()
                ->docsPage()
                ->render(),
        ]);
    }
}
