<?php

use Awcodes\ContentFaker\MarkdownFaker;

it('generates a heading', function () {
    expect(MarkdownFaker::make()->h1('Hello')->render())
        ->toContain('# Hello');
});

it('generates a paragraph', function () {
    expect(MarkdownFaker::make()->paragraph('Just some text.')->render())
        ->toContain('Just some text.');
});

it('casts to a string', function () {
    $faker = MarkdownFaker::make()->h2('Title');

    expect((string) $faker)->toBe($faker->render());
});

it('aliases render to toString', function () {
    $faker = MarkdownFaker::make()->h2('Title')->paragraph('Body.');

    expect($faker->render())->toBe($faker->toString());
});

it('generates a github alert', function () {
    expect(MarkdownFaker::make()->alert('Be careful.', 'WARNING')->render())
        ->toContain('> [!WARNING]')
        ->toContain('> Be careful.');
});

it('falls back to NOTE for invalid alert types', function () {
    expect(MarkdownFaker::make()->alert('Heads up.', 'BOGUS')->render())
        ->toContain('> [!NOTE]');
});

it('delegates callout to alert', function () {
    expect(MarkdownFaker::make()->callout('Tip text.', 'TIP')->render())
        ->toContain('> [!TIP]')
        ->toContain('> Tip text.');
});

it('supports opt-in front matter', function () {
    expect(MarkdownFaker::make()->frontMatter(['title' => 'Hello'])->render())
        ->toStartWith('---');
});

it('does not include front matter in presets', function () {
    expect(MarkdownFaker::make()->blogPost()->render())
        ->not->toStartWith('---');
});

it('can disable inline decorations', function () {
    $content = MarkdownFaker::make()
        ->inlineProbability(100)
        ->withoutInlineDecorations()
        ->paragraph('one two three four five six seven eight nine ten.')
        ->render();

    expect($content)->toBe('one two three four five six seven eight nine ten.');
});

it('clamps probabilities', function () {
    $faker = MarkdownFaker::make()
        ->inlineProbability(500)
        ->linkProbability(-10)
        ->maxInlineDecorationsPerParagraph(-5);

    expect($faker)->toBeInstanceOf(MarkdownFaker::class);
});

it('generates a docs page with expected sections', function () {
    expect(MarkdownFaker::make()->docsPage()->render())
        ->toContain('## Getting Started')
        ->toContain('## Usage');
});
