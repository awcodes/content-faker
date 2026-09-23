---
title: Usage
description: Build content with the fluent API, use presets, and tune how much inline formatting appears.
---

# Usage

## The fluent API

Every faker returns itself from each call, so content is built up in the order you chain it. Finish with `render()`:

```php
use Awcodes\ContentFaker\MarkdownFaker;

$markdown = MarkdownFaker::make()
    ->h1('Getting Started')
    ->paragraph()
    ->render();
```

`toString()` is an alias for `render()`, and all three fakers implement `Stringable`, so casting to string works too.

## Helper functions

Three helpers save the import:

```php
markdown_faker();
html_faker();
rich_editor_faker();
```

Each returns a fresh instance, equivalent to calling `make()` on the corresponding class.

## Blocks

The shared vocabulary across all three fakers:

| Method | Produces |
|---|---|
| `heading()`, `h1()`–`h6()` | A heading. `heading()` takes the level; the aliases are fixed. |
| `paragraph()`, `paragraphs()` | One or several paragraphs. |
| `unorderedList()`, `orderedList()`, `taskList()` | Bulleted, numbered and checkbox lists. |
| `codeBlock()` | A fenced code block, with a language. |
| `table()` | A table. |
| `blockquote()` | A quotation. |
| `image()` | An image. |
| `alert()` | A callout — see [Generators](generators.md). |
| `details()` | A collapsible section. |
| `horizontalRule()` | A rule. |
| `random()` | A random block, for filling space without deciding. |

Passing no argument lets the faker generate the text, so `->paragraph()` and `->h2('Installation')` are both valid — headings need their text.

## Inline formatting

Paragraph text is decorated with bold, italic, code and links at random, which is what makes the output look like real writing rather than filler. The whole system can be tuned:

| Method | Effect |
|---|---|
| `withInlineDecorations()` / `withoutInlineDecorations()` | Turn decoration on or off. |
| `inlineProbability()` | How often any decoration is applied. |
| `linkProbability()` | How often a link is used. |
| `codeProbability()` | How often inline code is used. |
| `maxInlineDecorationsPerParagraph()` | Cap per paragraph. |
| `inlineTypes()` | Which kinds are eligible. |

The decoration *types* differ per faker, since they emit different syntax — `bold()`, `italic()`, `boldItalic()` on `MarkdownFaker`, and `strong()`, `em()`, `strongEm()` on `HtmlFaker`. Both share `strikethrough()`, `inlineCode()` and `link()`.

## Presets

Each faker ships five presets that assemble a whole document in one call:

`article()`, `blogPost()`, `docsPage()`, `technicalDocs()`, `releaseNotes()`

```php
markdown_faker()->docsPage()->render();
```

`RichEditorFaker` overrides `article()` and `docsPage()` to include editor-specific content — lead paragraphs, buttons, callouts, columns and merge tags.

## In a factory

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

## Seeding and custom generators

By default, content comes from Laravel's `fake()` generator, or from a fresh `Faker\Factory::create()` outside Laravel. Every random choice goes through that generator, including where inline decorations land, so seeding Faker makes the output reproducible:

```php
fake()->seed(1234);

$markdown = markdown_faker()->docsPage()->render(); // identical on every run
```

To use a specific generator, for example one with your own providers, pass it to `make()` or a helper, or call `withFaker()`:

```php
use Faker\Factory;
use Faker\Provider\Lorem;

$faker = Factory::create();
$faker->addProvider(new class($faker) extends Lorem
{
    protected static $wordList = [
        'deploy', 'pipeline', 'release', 'cache', 'queue', 'worker',
        'schema', 'migration', 'endpoint', 'payload', 'webhook', 'token',
    ];
});

MarkdownFaker::make($faker)->article()->render();
markdown_faker($faker)->article()->render();
MarkdownFaker::make()->withFaker($faker)->article()->render();
```

Generated prose comes from Faker's `Lorem` provider, which no locale overrides, so a locale-specific generator still produces Latin text. To change the prose itself, add a provider that extends `Faker\Provider\Lorem` with your own `$wordList`, as above. Every word, sentence and paragraph is drawn from that list. Overriding individual methods such as `paragraph()` isn't enough, because `Lorem` calls its own methods internally rather than going through the generator.

> [!NOTE]
> Faker's `seed()` seeds PHP's process-wide random number generator, not the individual generator. Seeding any Faker instance affects all of them, so seed immediately before generating the content you want to reproduce.

## Configuration

The published config sets the defaults every faker starts from. The fluent methods above override it per instance.

```php
return [
    'inline_decorations' => true,
    'inline_probability' => 14,
    'link_probability' => 4,
    'code_probability' => 4,
    'max_inline_decorations_per_paragraph' => 3,

    'markdown' => [
        'inline_types' => ['bold', 'italic', 'bold_italic', 'strikethrough', 'code', 'link'],
        'alert_types' => ['NOTE', 'TIP', 'IMPORTANT', 'WARNING', 'CAUTION'],
    ],

    'html' => [
        'inline_types' => ['strong', 'em', 'strong_em', 's', 'code', 'link'],
        'alert_types' => ['note', 'tip', 'important', 'warning', 'caution'],
    ],

    'rich_editor' => [
        'merge_tags' => [
            'first_name', 'last_name', 'full_name', 'email',
            'company_name', 'unsubscribe_url', 'app_name',
        ],
        'merge_tag_format' => 'filament', // or 'text' for {{ key }}
        'filament_block_wrapper_class' => 'filament-block',
        'button_class' => 'button',
        'columns_class' => 'columns',
        'callout_class' => 'callout',
    ],
];
```

The probabilities are percentages, clamped to 0–100, and they are rolled **per sentence** rather than per paragraph — `decorateInline()` splits the text on sentence boundaries and tests each one, stopping once `max_inline_decorations_per_paragraph` is reached. So the default of `14` means roughly one sentence in seven picks up a decoration, up to three per paragraph.

The `rich_editor` class names land in the generated markup, so set them to whatever your own CSS expects.
