---
title: Content Faker
description: Generate realistic fake Markdown, HTML and rich editor content for Laravel factories, seeders and tests.
---

# Content Faker

Content Faker generates realistic fake **Markdown**, **HTML** and **rich editor** content — for model factories, database seeders, rich text previews, documentation examples, renderer tests, CMS demos and UI screenshots.

Where a plain `fake()->paragraph()` gives you flat prose, these fakers produce structured documents: headings, lists, tables, code blocks, callouts and inline formatting, in the shape a real editor would have written.

## The three generators

```text
MarkdownFaker
HtmlFaker
└── RichEditorFaker
```

`MarkdownFaker` and `HtmlFaker` are independent. `RichEditorFaker` extends `HtmlFaker`, so it has everything HTML has plus editor-specific blocks.

> [!NOTE]
> `RichEditorFaker` produces editor-friendly HTML only. It does **not** require Filament, Tiptap, ProseMirror, Livewire or any editor package — the Filament block helpers are placeholders in the output, nothing more.

## Requirements

- PHP 8.3 or later
- `fakerphp/faker` and `illuminate/support`

It is Laravel-friendly and works outside a full Laravel application where practical.

## Where to go next

- [Installation](installation.md) — install the package and publish its config.
- [Usage](usage.md) — the fluent API, presets, factory examples and configuration.
- [Generators](generators.md) — what each of the three adds.
