# Content Faker — Full Agent-Ready Implementation Specification

## 1. Project Summary

Build a PHP package from an empty directory.

Package name:

```txt
awcodes/content-faker
```

Namespace:

```php
Awcodes\ContentFaker
```

Primary goal:

Create a Laravel-friendly PHP package that generates realistic fake content for model factories, database seeders, rich text previews, documentation examples, renderer tests, CMS demos, and UI screenshots.

The package must include three generators:

```php
Awcodes\ContentFaker\MarkdownFaker
Awcodes\ContentFaker\HtmlFaker
Awcodes\ContentFaker\RichEditorFaker
```

Helpers:

```php
markdown_faker()
html_faker()
rich_editor_faker()
```

Inheritance:

```txt
MarkdownFaker

HtmlFaker
└── RichEditorFaker
```

Important constraint:

`RichEditorFaker` must not require Filament, Richer Editor, Tiptap, ProseMirror, Livewire, or any editor package. It is only an HTML-oriented faker with editor-friendly helper methods.

---

## 2. Non-Negotiable Requirements

- PHP 8.3+
- Laravel-friendly, but usable outside a full Laravel app where practical
- Dependency-light
- FakerPHP for fake text/data
- Illuminate Support allowed
- Pest tests
- Config publishing for Laravel
- Composer package with auto-discovery
- Fluent API returning `static`
- Safe HTML escaping
- HTML output must be article-body content only
- Markdown presets must not include front matter by default
- Front matter must be opt-in only
- RichEditorFaker must have merge tag helpers
- RichEditorFaker must have Filament block placeholder helpers
- No dependency on Filament
- No dependency on Richer Editor

---

## 3. Directory Structure

Create this exact structure:

```txt
.
├── composer.json
├── config/
│   └── content-faker.php
├── src/
│   ├── ContentFakerServiceProvider.php
│   ├── MarkdownFaker.php
│   ├── HtmlFaker.php
│   ├── RichEditorFaker.php
│   ├── helpers.php
│   └── Support/
│       └── EscapesHtml.php
├── tests/
│   ├── TestCase.php
│   ├── MarkdownFakerTest.php
│   ├── HtmlFakerTest.php
│   └── RichEditorFakerTest.php
├── phpunit.xml.dist
├── pest.php
├── README.md
├── LICENSE.md
└── .gitignore
```

---

## 4. composer.json

Create `composer.json`:

```json
{
    "name": "awcodes/content-faker",
    "description": "Generate fake Markdown, HTML, and rich editor content for Laravel factories, seeders, tests, and previews.",
    "type": "library",
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "fakerphp/faker": "^1.23",
        "illuminate/support": "^11.0|^12.0|^13.0"
    },
    "require-dev": {
        "orchestra/testbench": "^9.0|^10.0|^11.0",
        "pestphp/pest": "^3.0|^4.0",
        "pestphp/pest-plugin-phpunit": "^3.0|^4.0"
    },
    "autoload": {
        "psr-4": {
            "Awcodes\\ContentFaker\\": "src/"
        },
        "files": [
            "src/helpers.php"
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Awcodes\\ContentFaker\\Tests\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Awcodes\\ContentFaker\\ContentFakerServiceProvider"
            ]
        }
    },
    "scripts": {
        "test": "pest",
        "test:coverage": "pest --coverage"
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

---

## 5. Configuration

Create `config/content-faker.php`.

```php
<?php

return [
    'inline_decorations' => true,

    'inline_probability' => 14,

    'link_probability' => 4,

    'code_probability' => 4,

    'max_inline_decorations_per_paragraph' => 3,

    'markdown' => [
        'inline_types' => [
            'bold',
            'italic',
            'bold_italic',
            'strikethrough',
            'code',
            'link',
        ],

        'alert_types' => [
            'NOTE',
            'TIP',
            'IMPORTANT',
            'WARNING',
            'CAUTION',
        ],
    ],

    'html' => [
        'inline_types' => [
            'strong',
            'em',
            'strong_em',
            's',
            'code',
            'link',
        ],

        'alert_types' => [
            'note',
            'tip',
            'important',
            'warning',
            'caution',
        ],
    ],

    'rich_editor' => [
        'merge_tags' => [
            'first_name',
            'last_name',
            'full_name',
            'email',
            'company_name',
            'unsubscribe_url',
            'app_name',
        ],

        'filament_block_wrapper_class' => 'filament-block',

        'button_class' => 'button',

        'columns_class' => 'columns',

        'callout_class' => 'callout',
    ],
];
```

---

## 6. Service Provider

Create `src/ContentFakerServiceProvider.php`.

Requirements:

- Merge config
- Publish config
- Bind all faker classes into the container
- Do not require a Laravel app for direct static usage

Implementation:

```php
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
```

---

## 7. Helpers

Create `src/helpers.php`.

```php
<?php

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\MarkdownFaker;
use Awcodes\ContentFaker\RichEditorFaker;

if (! function_exists('markdown_faker')) {
    function markdown_faker(): MarkdownFaker
    {
        return MarkdownFaker::make();
    }
}

if (! function_exists('html_faker')) {
    function html_faker(): HtmlFaker
    {
        return HtmlFaker::make();
    }
}

if (! function_exists('rich_editor_faker')) {
    function rich_editor_faker(): RichEditorFaker
    {
        return RichEditorFaker::make();
    }
}
```

---

## 8. Support Trait

Create `src/Support/EscapesHtml.php`.

```php
<?php

namespace Awcodes\ContentFaker\Support;

trait EscapesHtml
{
    protected function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
```

---

## 9. Shared API Requirements

All three fakers must support:

```php
public static function make(): static;

public function render(): string;

public function toString(): string;

public function __toString(): string;

public function withoutInlineDecorations(): static;

public function withInlineDecorations(bool $condition = true): static;

public function inlineProbability(int $percentage): static;

public function linkProbability(int $percentage): static;

public function codeProbability(int $percentage): static;

public function maxInlineDecorationsPerParagraph(int $count): static;

public function inlineTypes(array $types): static;
```

Behavior:

- `render()` and `toString()` return the same result.
- `__toString()` must not throw.
- Percentages must clamp to 0–100.
- Max inline decorations must not be negative.

---

## 10. MarkdownFaker Specification

Create `src/MarkdownFaker.php`.

### Properties

```php
protected array $blocks = [];

protected bool $inlineDecorations = true;

protected int $inlineProbability = 14;

protected int $linkProbability = 4;

protected int $codeProbability = 4;

protected int $maxInlineDecorationsPerParagraph = 3;

protected array $inlineTypes = [
    'bold',
    'italic',
    'bold_italic',
    'strikethrough',
    'code',
    'link',
];

protected array $alertTypes = [
    'NOTE',
    'TIP',
    'IMPORTANT',
    'WARNING',
    'CAUTION',
];
```

### Constructor

Read config values when available, with hardcoded fallbacks.

Because the class should still work outside Laravel, guard `config()` usage:

```php
protected function configValue(string $key, mixed $default = null): mixed
{
    if (function_exists('config')) {
        return config($key, $default);
    }

    return $default;
}
```

### Required Markdown Methods

```php
heading(string $text, int $level = 2): static
h1(string $text): static
h2(string $text): static
h3(string $text): static
h4(string $text): static
h5(string $text): static
h6(string $text): static

paragraph(?string $text = null): static
paragraphs(int $count = 3): static

bold(string $text): static
italic(string $text): static
boldItalic(string $text): static
strikethrough(string $text): static
inlineCode(string $code): static

link(?string $label = null, ?string $url = null): static
image(?string $alt = null, ?string $url = null): static

blockquote(?string $text = null): static

unorderedList(array|int $items = 3): static
orderedList(array|int $items = 3): static
taskList(array|int $items = 3): static

codeBlock(?string $code = null, string $language = 'php'): static

horizontalRule(): static

table(array $headers = ['Name', 'Value'], array $rows = []): static

footnote(?string $label = null, ?string $content = null): static

alert(?string $text = null, string $type = 'TIP'): static
callout(?string $text = null, string $type = 'NOTE'): static

details(?string $summary = null, ?string $content = null): static

frontMatter(array $data = []): static

random(int $blocks = 8): static

article(): static
blogPost(): static
docsPage(): static
technicalDocs(): static
releaseNotes(): static
```

### Markdown Inline Decoration

Implement protected methods:

```php
protected function decorateInline(string $text): string;

protected function decorateSentence(string $sentence): string;

protected function randomInline(string $text): string;

protected function maybeDecorateListItem(string $item): string;

protected function isSafeInlinePhrase(string $text): bool;

protected function fakeInlineCode(): string;

protected function fakeCodeBlock(string $language): string;
```

Rules:

- Split paragraphs into sentences.
- Decorate up to `maxInlineDecorationsPerParagraph`.
- Each decoration chooses a 1–3 word phrase.
- Skip unsafe markdown syntax.
- Do not decorate headings.
- Do not decorate URLs.
- Do not decorate image alt text.
- Do not create nested inline markdown.

Unsafe phrase characters for markdown:

```txt
[ ] ( ) ` * _ ~ # | < >
```

### Markdown Alert Format

`alert('Message', 'TIP')` outputs:

```md
> [!TIP]
> Message
```

For multi-line text:

```md
> [!TIP]
> First line.
> Second line.
```

Allowed alert types:

```txt
NOTE
TIP
IMPORTANT
WARNING
CAUTION
```

Invalid types should fall back to `NOTE`.

`callout()` delegates to `alert()`.

### Front Matter

`frontMatter()` is opt-in only.

Presets must not include front matter.

Example:

```php
markdown_faker()
    ->frontMatter([
        'title' => 'Example',
        'description' => 'Example description',
    ])
    ->docsPage()
    ->render();
```

YAML can be simple key/value output. Use `json_encode()` for values to avoid invalid scalar output.

---

## 11. HtmlFaker Specification

Create `src/HtmlFaker.php`.

Use:

```php
use Awcodes\ContentFaker\Support\EscapesHtml;
```

### Properties

```php
protected array $blocks = [];

protected bool $inlineDecorations = true;

protected int $inlineProbability = 14;

protected int $linkProbability = 4;

protected int $codeProbability = 4;

protected int $maxInlineDecorationsPerParagraph = 3;

protected array $inlineTypes = [
    'strong',
    'em',
    'strong_em',
    's',
    'code',
    'link',
];

protected array $alertTypes = [
    'note',
    'tip',
    'important',
    'warning',
    'caution',
];
```

### Required HTML Methods

```php
heading(string $text, int $level = 2): static
h1(string $text): static
h2(string $text): static
h3(string $text): static
h4(string $text): static
h5(string $text): static
h6(string $text): static

paragraph(?string $text = null): static
paragraphs(int $count = 3): static

strong(string $text): static
em(string $text): static
strongEm(string $text): static
strikethrough(string $text): static
inlineCode(string $code): static

link(?string $label = null, ?string $url = null): static

image(?string $alt = null, ?string $url = null): static
figure(?string $alt = null, ?string $caption = null, ?string $url = null): static

blockquote(?string $text = null): static

unorderedList(array|int $items = 3): static
orderedList(array|int $items = 3): static
taskList(array|int $items = 3): static

codeBlock(?string $code = null, string $language = 'php'): static

horizontalRule(): static

table(array $headers = ['Name', 'Value'], array $rows = []): static

alert(?string $text = null, string $type = 'tip'): static

details(?string $summary = null, ?string $content = null): static

random(int $blocks = 8): static

article(): static
blogPost(): static
docsPage(): static
technicalDocs(): static
releaseNotes(): static
```

### HTML Requirements

Output article-body HTML only.

Never output:

```html
<html>
<head>
<body>
```

Escape all generated text.

Escape all attribute values.

Generated tags may be raw.

Never decorate attributes.

Never put inline tags inside:

```txt
src
alt
href
title
class
data-*
```

Image output:

```html
<img src="..." alt="...">
```

Figure output:

```html
<figure>
    <img src="..." alt="...">
    <figcaption>...</figcaption>
</figure>
```

The caption is text content and may be decorated only if intentionally designed. Safer default: escape caption without decoration.

### HTML Inline Decoration

Implement protected methods:

```php
protected function decorateInline(string $text): string;

protected function decorateSentence(string $sentence): ?string;

protected function randomInline(string $text): string;

protected function decorateListItem(string $item): string;

protected function isSafeInlinePhrase(string $text): bool;

protected function fakeInlineCode(): string;

protected function fakeCodeBlock(string $language): string;
```

Rules:

- Decorate text nodes only.
- Escape every undecorated word.
- Escape decorated text inside inline tags.
- Escape links.
- Do not decorate if text contains unsafe HTML characters.

Unsafe phrase characters for HTML:

```txt
< > & " '
```

### HTML Alert Format

Output:

```html
<div class="alert alert-tip" role="note">
    <p>Message here.</p>
</div>
```

Allowed types:

```txt
note
tip
important
warning
caution
```

Invalid types should fall back to `note`.

---

## 12. RichEditorFaker Specification

Create `src/RichEditorFaker.php`.

It must extend `HtmlFaker`.

```php
<?php

namespace Awcodes\ContentFaker;

class RichEditorFaker extends HtmlFaker
{
    //
}
```

No dependency on Filament.

No dependency on Richer Editor.

No runtime detection.

No optional composer suggestion is required.

Purpose:

Generate rich-editor-friendly HTML content for factories, seeders, previews, and tests.

### Additional Properties

```php
protected array $mergeTags = [
    'first_name',
    'last_name',
    'full_name',
    'email',
    'company_name',
    'unsubscribe_url',
    'app_name',
];

protected string $buttonClass = 'button';

protected string $columnsClass = 'columns';

protected string $calloutClass = 'callout';

protected string $filamentBlockWrapperClass = 'filament-block';
```

Read config values when available.

### Required Additional Methods

```php
lead(?string $text = null): static;

small(?string $text = null): static;

button(?string $label = null, ?string $url = null): static;

buttonGroup(array|int $buttons = 2): static;

callout(?string $text = null, string $type = 'tip'): static;

columns(array|int $columns = 2): static;

customPlaceholder(string $name, array $data = []): static;

mergeTag(string $key, ?string $fallback = null): static;

paragraphWithMergeTags(array $tags = []): static;

headingWithMergeTags(int $level = 2, array $tags = []): static;

filamentBlock(string $type, array $data = []): static;

filamentBlocks(array $blocks): static;
```

### Rich Editor Method Details

#### lead()

Output:

```html
<p class="lead">...</p>
```

#### small()

Output:

```html
<p><small>...</small></p>
```

#### button()

Output:

```html
<a href="..." class="button">Label</a>
```

If URL is omitted, use `#`.

Escape label and URL.

#### buttonGroup()

Output:

```html
<div class="button-group">
    <a href="#" class="button">First</a>
    <a href="#" class="button">Second</a>
</div>
```

Accept either:

```php
buttonGroup(3)
```

or:

```php
buttonGroup([
    ['label' => 'Get Started', 'url' => '/start'],
    ['label' => 'Learn More', 'url' => '/docs'],
])
```

#### callout()

Overrides or aliases `alert()` with editor-friendly classes.

Output:

```html
<div class="callout callout-tip" role="note">
    <p>Message</p>
</div>
```

#### columns()

Accept int:

```php
columns(3)
```

Output:

```html
<div class="columns columns-3">
    <div>
        <p>...</p>
    </div>
    <div>
        <p>...</p>
    </div>
    <div>
        <p>...</p>
    </div>
</div>
```

Accept array:

```php
columns([
    'First column content',
    'Second column content',
])
```

Escape column content. It may be decorated as text-node content.

#### customPlaceholder()

Output a generic editor placeholder:

```html
<div data-placeholder="name" data-config="...">
    Placeholder: name
</div>
```

The `data-config` value should be JSON encoded and escaped.

#### mergeTag()

Output:

```html
{{ first_name }}
```

If fallback is supplied:

```html
{{ first_name|Guest }}
```

Do not HTML-escape the curly braces themselves. Escape the key/fallback content before composing or validate key names strictly.

Recommended validation:

- Only allow letters, numbers, underscores, hyphens, and dots in merge tag keys.
- If invalid, fallback to `value`.

#### paragraphWithMergeTags()

Default output:

```html
<p>Hello {{ first_name }}, welcome to {{ company_name }}.</p>
```

Accept custom tags:

```php
paragraphWithMergeTags(['first_name', 'company_name'])
```

#### headingWithMergeTags()

Output:

```html
<h2>Welcome, {{ first_name }}</h2>
```

Clamp heading level 1–6.

#### filamentBlock()

This is a placeholder only.

No Filament dependency.

Output:

```html
<!-- filament-block: hero -->
<div class="filament-block" data-block="filament" data-type="hero">
    <h2>Hero</h2>
    <p>Generated block content.</p>
</div>
```

If data includes `heading`, use it.

If data includes `subheading`, use it.

If data includes `content`, use it.

If data includes arbitrary values, include them as escaped `data-*` attributes when scalar.

Example:

```php
filamentBlock('hero', [
    'heading' => '{{ company_name }}',
    'subheading' => 'Build faster.',
    'variant' => 'centered',
])
```

Possible output:

```html
<!-- filament-block: hero -->
<div class="filament-block" data-block="filament" data-type="hero" data-variant="centered">
    <h2>{{ company_name }}</h2>
    <p>Build faster.</p>
</div>
```

#### filamentBlocks()

Accept:

```php
filamentBlocks([
    [
        'type' => 'hero',
        'data' => [
            'heading' => '{{ company_name }}',
        ],
    ],
    [
        'type' => 'cta',
        'data' => [
            'label' => 'Get Started',
        ],
    ],
])
```

Loop over and call `filamentBlock()`.

---

## 13. Presets

Each class should implement these presets:

```php
article()
blogPost()
docsPage()
technicalDocs()
releaseNotes()
```

### Markdown Presets

Do not include front matter.

### Html Presets

Do not include `<html>`, `<head>`, or `<body>`.

### RichEditor Presets

Can inherit `HtmlFaker` presets, but should be allowed to override selected presets to include editor-friendly content such as:

- lead paragraph
- button
- button group
- callout
- columns
- merge tag paragraphs
- filament block placeholders

At minimum, override `docsPage()` and `article()` in `RichEditorFaker` to include at least one rich-editor-specific method.

---

## 14. Example Usage

### Markdown

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

### HTML

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

### Rich Editor

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

### Factory Example

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

---

## 15. Testing Requirements

Use Pest.

Create `tests/TestCase.php` extending Orchestra Testbench.

Register the service provider.

### Markdown Tests

Test:

- heading output
- paragraph output
- string casting
- render aliases to toString
- alert output
- invalid alert falls back to NOTE
- front matter works
- presets do not include front matter
- inline decoration can be disabled
- probabilities can be configured
- docsPage includes expected sections

### Html Tests

Test:

- heading output
- paragraph output
- string casting
- render aliases to toString
- escaping text
- escaping code blocks
- no `<html>`
- no `<body>`
- image attributes are not decorated
- figure attributes are not decorated
- alert output
- invalid alert falls back to note
- docsPage includes expected sections

### RichEditor Tests

Test:

- extends HtmlFaker
- lead output
- small output
- button output
- buttonGroup output
- callout output
- columns int output
- columns array output
- mergeTag output
- mergeTag validates invalid keys
- paragraphWithMergeTags output
- headingWithMergeTags output
- filamentBlock output
- filamentBlocks output
- customPlaceholder output
- inherited HTML methods still work

---

## 16. Example Pest Tests

### Markdown

```php
<?php

use Awcodes\ContentFaker\MarkdownFaker;

it('generates a heading', function () {
    expect(MarkdownFaker::make()->h1('Hello')->render())
        ->toContain('# Hello');
});

it('generates a github alert', function () {
    expect(MarkdownFaker::make()->alert('Be careful.', 'WARNING')->render())
        ->toContain('> [!WARNING]')
        ->toContain('> Be careful.');
});

it('does not include front matter in presets', function () {
    expect(MarkdownFaker::make()->blogPost()->render())
        ->not->toStartWith('---');
});

it('supports opt-in front matter', function () {
    expect(MarkdownFaker::make()->frontMatter(['title' => 'Hello'])->render())
        ->toStartWith('---');
});
```

### HTML

```php
<?php

use Awcodes\ContentFaker\HtmlFaker;

it('escapes paragraph content', function () {
    expect(HtmlFaker::make()->paragraph('<script>alert("x")</script>')->render())
        ->toContain('&lt;script&gt;')
        ->not->toContain('<script>');
});

it('does not output document wrappers', function () {
    $content = HtmlFaker::make()->article()->render();

    expect($content)
        ->not->toContain('<html')
        ->not->toContain('<body');
});

it('does not decorate image attributes', function () {
    $content = HtmlFaker::make()
        ->inlineProbability(100)
        ->image('Example Image')
        ->render();

    expect($content)
        ->toContain('alt="Example Image"')
        ->not->toContain('<strong>Example Image</strong>');
});
```

### Rich Editor

```php
<?php

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\RichEditorFaker;

it('extends the html faker', function () {
    expect(RichEditorFaker::make())->toBeInstanceOf(HtmlFaker::class);
});

it('generates merge tags', function () {
    expect(RichEditorFaker::make()->mergeTag('first_name')->render())
        ->toContain('{{ first_name }}');
});

it('generates filament block placeholders', function () {
    $content = RichEditorFaker::make()
        ->filamentBlock('hero', ['heading' => '{{ company_name }}'])
        ->render();

    expect($content)
        ->toContain('filament-block: hero')
        ->toContain('data-block="filament"')
        ->toContain('data-type="hero"')
        ->toContain('{{ company_name }}');
});
```

---

## 17. README Requirements

Create a README containing:

- Package name
- Installation
- Basic usage
- Markdown usage
- HTML usage
- RichEditor usage
- Laravel factory examples
- Config publishing
- Available methods
- Presets
- Security/escaping notes
- Front matter opt-in note
- Testing instructions

Install command:

```bash
composer require awcodes/content-faker
```

Publish config:

```bash
php artisan vendor:publish --tag="content-faker-config"
```

---

## 18. Implementation Order

Follow this order:

1. Create composer.json.
2. Create directory structure.
3. Create config.
4. Create service provider.
5. Create helpers.
6. Create EscapesHtml trait.
7. Implement MarkdownFaker.
8. Implement HtmlFaker.
9. Implement RichEditorFaker.
10. Create Pest setup.
11. Add tests.
12. Add README.
13. Run composer install.
14. Run tests.
15. Fix failures.
16. Ensure package can be autoloaded.
17. Ensure examples work.

---

## 19. Acceptance Criteria

The implementation is complete when:

- `composer install` succeeds.
- `composer test` succeeds.
- All Pest tests pass.
- `markdown_faker()->docsPage()->render()` returns Markdown.
- `html_faker()->article()->render()` returns article-body HTML.
- `rich_editor_faker()->filamentBlock('hero')->render()` returns HTML.
- Markdown presets do not include front matter.
- HTML output does not include document wrapper tags.
- HTML escaping works.
- Image attributes are never decorated.
- RichEditorFaker has merge tag support.
- RichEditorFaker has Filament block placeholder support.
- No Filament package is required.
- No Richer Editor package is required.

---

## 20. Final Commands

```bash
composer install
composer test
vendor/bin/pest
```

All must pass.
