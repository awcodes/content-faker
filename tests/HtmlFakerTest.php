<?php

use Awcodes\ContentFaker\HtmlFaker;

it('generates a heading', function (): void {
    expect(HtmlFaker::make()->h1('Hello')->render())
        ->toContain('<h1>Hello</h1>');
});

it('generates a paragraph', function (): void {
    expect(HtmlFaker::make()->withoutInlineDecorations()->paragraph('Just text.')->render())
        ->toContain('<p>Just text.</p>');
});

it('casts to a string', function (): void {
    $faker = HtmlFaker::make()->h2('Title');

    expect((string) $faker)->toBe($faker->render());
});

it('aliases render to toString', function (): void {
    $faker = HtmlFaker::make()->h2('Title')->paragraph('Body.');

    expect($faker->render())->toBe($faker->toString());
});

it('escapes paragraph content', function (): void {
    expect(HtmlFaker::make()->paragraph('<script>alert("x")</script>')->render())
        ->toContain('&lt;script&gt;')
        ->not->toContain('<script>');
});

it('escapes code blocks', function (): void {
    expect(HtmlFaker::make()->codeBlock('<?php echo "hi"; ?>')->render())
        ->toContain('&lt;?php')
        ->not->toContain('<?php echo');
});

it('does not output document wrappers', function (): void {
    $content = HtmlFaker::make()->article()->render();

    expect($content)
        ->not->toContain('<html')
        ->not->toContain('<head')
        ->not->toContain('<body');
});

it('does not decorate image attributes', function (): void {
    $content = HtmlFaker::make()
        ->inlineProbability(100)
        ->image('Example Image')
        ->render();

    expect($content)
        ->toContain('alt="Example Image"')
        ->not->toContain('<strong>Example Image</strong>');
});

it('does not decorate figure attributes', function (): void {
    $content = HtmlFaker::make()
        ->inlineProbability(100)
        ->figure('Example Image', 'A caption')
        ->render();

    expect($content)
        ->toContain('alt="Example Image"')
        ->not->toContain('alt="<strong>');
});

it('generates an alert', function (): void {
    expect(HtmlFaker::make()->alert('Heads up.', 'warning')->render())
        ->toContain('class="alert alert-warning"')
        ->toContain('role="note"');
});

it('falls back to note for invalid alert types', function (): void {
    expect(HtmlFaker::make()->alert('Heads up.', 'bogus')->render())
        ->toContain('class="alert alert-note"');
});

it('generates a docs page with expected sections', function (): void {
    expect(HtmlFaker::make()->docsPage()->render())
        ->toContain('<h2>Getting Started</h2>')
        ->toContain('<h2>Usage</h2>');
});
