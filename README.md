# Content Faker

Generate realistic fake **Markdown**, **HTML**, and **rich editor** content for Laravel model factories, database seeders, rich text previews, documentation examples, renderer tests, CMS demos, and UI screenshots.

The package ships three fluent generators:

```
MarkdownFaker
HtmlFaker
└── RichEditorFaker
```

`RichEditorFaker` is editor-friendly HTML only — it does **not** require Filament, Tiptap, ProseMirror, Livewire, or any editor package.

## Requirements

- PHP 8.3+
- Laravel-friendly (works outside a full Laravel app where practical)

## Installation

```bash
composer require awcodes/content-faker
```

The service provider is auto-discovered. To customize defaults, publish the config:

```bash
php artisan vendor:publish --tag="content-faker-config"
```

## Basic Usage

Every faker exposes a fluent API that returns itself, plus `render()` / `toString()` / string casting:

```php
use Awcodes\ContentFaker\MarkdownFaker;

$markdown = MarkdownFaker::make()
    ->h1('Getting Started')
    ->paragraph()
    ->render();
```

Helper functions are also available:

```php
markdown_faker();
html_faker();
rich_editor_faker();
```

## Markdown Usage

```php
use Awcodes\ContentFaker\MarkdownFaker;

$content = MarkdownFaker::make()
    ->h1('Getting Started')
    ->paragraph()
    ->alert('Make sure your environment is configured.', 'IMPORTANT')
    ->h2('Installation')
    ->codeBlock('composer require vendor/package', 'bash')
    ->unorderedList([
        'Install dependencies',
        'Publish the config',
        'Run migrations',
    ])
    ->render();
```

GitHub-style alerts use `NOTE`, `TIP`, `IMPORTANT`, `WARNING`, `CAUTION` (invalid types fall back to `NOTE`). `callout()` delegates to `alert()`.

## HTML Usage

```php
use Awcodes\ContentFaker\HtmlFaker;

$content = HtmlFaker::make()
    ->h1('Getting Started')
    ->paragraphs(2)
    ->figure()
    ->h2('Features')
    ->unorderedList([
        'Safe HTML escaping',
        'Inline formatting',
        'Tables',
    ])
    ->render();
```

HTML output is **article-body content only** — never `<html>`, `<head>`, or `<body>`. All text and attribute values are escaped.

## Rich Editor Usage

```php
use Awcodes\ContentFaker\RichEditorFaker;

$content = RichEditorFaker::make()
    ->h1('Welcome')
    ->paragraphWithMergeTags(['first_name', 'company_name'])
    ->lead()
    ->button('Get Started', '/start')
    ->filamentBlock('hero', [
        'heading' => '{{ company_name }}',
        'subheading' => 'Build faster with realistic demo content.',
    ])
    ->columns(2)
    ->render();
```

Merge tags render as `{{ key }}` or `{{ key|fallback }}`. Keys are validated (letters, numbers, underscores, hyphens, dots); invalid keys fall back to `value`. Filament block helpers are placeholders only and require no Filament dependency.

## Laravel Factory Examples

```php
public function definition(): array
{
    return [
        'title' => fake()->sentence(),

        'markdown_content' => markdown_faker()
            ->docsPage()
            ->render(),

        'html_content' => html_faker()
            ->article()
            ->render(),

        'rich_content' => rich_editor_faker()
            ->paragraphWithMergeTags()
            ->filamentBlock('cta')
            ->render(),
    ];
}
```

## Presets

Each faker implements: `article()`, `blogPost()`, `docsPage()`, `technicalDocs()`, `releaseNotes()`. `RichEditorFaker` overrides `article()` and `docsPage()` to include editor-specific content (lead paragraphs, buttons, callouts, columns, merge tags).

## Available Methods

Shared across all fakers: `make()`, `render()`, `toString()`, `__toString()`, `withInlineDecorations()`, `withoutInlineDecorations()`, `inlineProbability()`, `linkProbability()`, `codeProbability()`, `maxInlineDecorationsPerParagraph()`, `inlineTypes()`, plus `heading()`/`h1()`–`h6()`, `paragraph()`, `paragraphs()`, lists, `codeBlock()`, `table()`, `blockquote()`, `image()`, `alert()`, `details()`, `horizontalRule()`, and `random()`.

`RichEditorFaker` adds: `lead()`, `small()`, `button()`, `buttonGroup()`, `callout()`, `columns()`, `customPlaceholder()`, `mergeTag()`, `paragraphWithMergeTags()`, `headingWithMergeTags()`, `filamentBlock()`, `filamentBlocks()`.

## Security & Escaping

`HtmlFaker` and `RichEditorFaker` escape all generated text and attribute values via `htmlspecialchars()` with `ENT_QUOTES | ENT_SUBSTITUTE`. Inline decorations are only applied to text nodes — image/figure attributes and other attribute values are never decorated.

## Front Matter

Markdown front matter is **opt-in only** via `frontMatter()`. Presets never include front matter.

```php
markdown_faker()
    ->frontMatter(['title' => 'Example', 'description' => 'Example description'])
    ->docsPage()
    ->render();
```

## Development

The repository ships an Orchestra Testbench Workbench — a small Laravel application, under `workbench/`, that consumes the package the way a real application would.

```bash
composer install   # install dependencies
composer test      # run Rector, Pint, Larastan and Pest
composer build     # build the Workbench database and seed it
composer serve     # start the Workbench at http://127.0.0.1:8000
```

`composer serve` builds first, so a fresh clone needs nothing else.

The Workbench seeds four `Workbench\App\Models\Post` records whose `markdown_content`, `html_content` and `rich_content` columns are filled by `Workbench\Database\Factories\PostFactory` using the three fakers. Each post page shows every generator's output rendered and as source, so changes to a faker are visible immediately:

```text
/                            list of seeded posts
/posts/{slug}                Markdown, HTML and rich editor output side by side
/posts/undecorated-example   the same presets with inline decorations disabled
```

No authentication is required, and no database server, Node toolchain or separate Laravel application is needed.

## Code Style

The package uses [Laravel Pint](https://laravel.com/docs/pint) for formatting, [Rector](https://getrector.com) for automated refactoring, and [Larastan](https://github.com/larastan/larastan) for static analysis:

```bash
composer format     # apply Pint fixes
composer lint       # check formatting without changing files
composer rector     # apply Rector refactorings
composer rector:dry # preview Rector changes
composer analyse    # run Larastan static analysis
```

## License

MIT — see [LICENSE.md](LICENSE.md).
