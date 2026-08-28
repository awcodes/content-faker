---
title: Generators
description: What MarkdownFaker, HtmlFaker and RichEditorFaker each produce and add.
---

# Generators

All three share the vocabulary in [Usage](usage.md). This page covers what each one adds.

## MarkdownFaker

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

### Alerts

`alert()` emits a GitHub-style alert. The types are `NOTE`, `TIP`, `IMPORTANT`, `WARNING` and `CAUTION`; the argument is upper-cased before matching, and anything unrecognised falls back to `NOTE`. Omit the type and you get `TIP`.

`callout()` delegates to `alert()`, so the two are interchangeable here.

### Footnotes

`footnote()` adds a Markdown footnote — a block type the HTML faker has no equivalent for.

### Front matter

Front matter is opt-in only. Presets never include it, so add it yourself when you want it:

```php
markdown_faker()
    ->frontMatter(['title' => 'Example', 'description' => 'Example description'])
    ->docsPage()
    ->render();
```

## HtmlFaker

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

Output is **article-body content only** — never `<html>`, `<head>` or `<body>` — so it drops straight into a template.

`figure()` adds a figure with a caption, which Markdown has no direct equivalent for.

### Escaping

All generated text and attribute values pass through `htmlspecialchars()` with `ENT_QUOTES | ENT_SUBSTITUTE` in UTF-8. Inline decorations are applied to text nodes only, so image and figure attributes are never decorated and cannot be broken by a stray tag.

## RichEditorFaker

`RichEditorFaker` extends `HtmlFaker`, so everything above applies, plus editor-shaped blocks.

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

| Method | Produces |
|---|---|
| `lead()`, `small()` | Emphasised and de-emphasised paragraphs. |
| `button()`, `buttonGroup()` | A call to action, or several. |
| `callout()` | A callout block. |
| `columns()` | A multi-column layout. |
| `mergeTag()` | A single merge tag in its own paragraph. |
| `paragraphWithMergeTags()`, `headingWithMergeTags()` | Text with tags woven through it. |
| `filamentBlock()`, `filamentBlocks()` | Placeholder custom blocks. |
| `customPlaceholder()` | An arbitrary placeholder block. |

### Merge tags

Tags render as `{{ key }}`, or `{{ key|fallback }}` when a fallback is given. Keys are validated against letters, numbers, underscores, hyphens and dots — anything else is replaced with `value`, so a malformed key degrades rather than emitting broken output. Fallback text is escaped.

The default tag names come from `rich_editor.merge_tags` in the config.

> [!NOTE]
> `filamentBlock()` and `filamentBlocks()` emit placeholder markup wrapped in the configured class. They require no Filament dependency and do not produce real Filament blocks — they exist so rich content has something block-shaped in it.
