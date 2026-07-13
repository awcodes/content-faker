<?php

declare(strict_types=1);

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\RichEditorFaker;

it('extends the html faker', function (): void {
    expect(RichEditorFaker::make())->toBeInstanceOf(HtmlFaker::class);
});

it('generates a lead paragraph', function (): void {
    expect(RichEditorFaker::make()->lead('Intro text.')->render())
        ->toContain('<p class="lead">');
});

it('generates small text', function (): void {
    expect(RichEditorFaker::make()->small('Fine print.')->render())
        ->toContain('<small>')
        ->toContain('Fine print.');
});

it('generates a button', function (): void {
    expect(RichEditorFaker::make()->button('Get Started', '/start')->render())
        ->toContain('<a href="/start" class="button">Get Started</a>');
});

it('defaults a button url to a hash', function (): void {
    expect(RichEditorFaker::make()->button('Click')->render())
        ->toContain('href="#"');
});

it('generates a button group from an int', function (): void {
    expect(RichEditorFaker::make()->buttonGroup(2)->render())
        ->toContain('<div class="button-group">')
        ->toContain('class="button"');
});

it('generates a button group from an array', function (): void {
    $content = RichEditorFaker::make()->buttonGroup([
        ['label' => 'Get Started', 'url' => '/start'],
        ['label' => 'Learn More', 'url' => '/docs'],
    ])->render();

    expect($content)
        ->toContain('href="/start"')
        ->toContain('Get Started')
        ->toContain('href="/docs"');
});

it('generates a callout', function (): void {
    expect(RichEditorFaker::make()->callout('Message', 'tip')->render())
        ->toContain('class="callout callout-tip"')
        ->toContain('role="note"');
});

it('generates columns from an int', function (): void {
    expect(RichEditorFaker::make()->columns(3)->render())
        ->toContain('class="columns columns-3"');
});

it('generates columns from an array', function (): void {
    $content = RichEditorFaker::make()->withoutInlineDecorations()->columns([
        'First column content',
        'Second column content',
    ])->render();

    expect($content)
        ->toContain('class="columns columns-2"')
        ->toContain('First column content');
});

it('generates a merge tag', function (): void {
    expect(RichEditorFaker::make()->mergeTag('first_name')->render())
        ->toContain('{{ first_name }}');
});

it('generates a merge tag with a fallback', function (): void {
    expect(RichEditorFaker::make()->mergeTag('first_name', 'Guest')->render())
        ->toContain('{{ first_name|Guest }}');
});

it('validates invalid merge tag keys', function (): void {
    expect(RichEditorFaker::make()->mergeTag('bad key!')->render())
        ->toContain('{{ value }}')
        ->not->toContain('bad key!');
});

it('generates a paragraph with default merge tags', function (): void {
    expect(RichEditorFaker::make()->paragraphWithMergeTags()->render())
        ->toContain('{{ first_name }}')
        ->toContain('{{ company_name }}');
});

it('generates a paragraph with custom merge tags', function (): void {
    expect(RichEditorFaker::make()->paragraphWithMergeTags(['email'])->render())
        ->toContain('{{ email }}');
});

it('generates a heading with merge tags', function (): void {
    expect(RichEditorFaker::make()->headingWithMergeTags(2, ['first_name'])->render())
        ->toContain('<h2>')
        ->toContain('{{ first_name }}');
});

it('generates a filament block placeholder', function (): void {
    $content = RichEditorFaker::make()
        ->filamentBlock('hero', ['heading' => '{{ company_name }}'])
        ->render();

    expect($content)
        ->toContain('filament-block: hero')
        ->toContain('data-block="filament"')
        ->toContain('data-type="hero"')
        ->toContain('{{ company_name }}');
});

it('includes scalar data as data attributes on filament blocks', function (): void {
    expect(RichEditorFaker::make()->filamentBlock('hero', ['variant' => 'centered'])->render())
        ->toContain('data-variant="centered"');
});

it('generates multiple filament blocks', function (): void {
    $content = RichEditorFaker::make()->filamentBlocks([
        ['type' => 'hero', 'data' => ['heading' => 'Welcome']],
        ['type' => 'cta', 'data' => ['heading' => 'Get Started']],
    ])->render();

    expect($content)
        ->toContain('data-type="hero"')
        ->toContain('data-type="cta"');
});

it('generates a custom placeholder', function (): void {
    expect(RichEditorFaker::make()->customPlaceholder('name', ['foo' => 'bar'])->render())
        ->toContain('data-placeholder="name"')
        ->toContain('Placeholder: name');
});

it('inherits html methods', function (): void {
    expect(RichEditorFaker::make()->h2('Heading')->render())
        ->toContain('<h2>Heading</h2>');
});
