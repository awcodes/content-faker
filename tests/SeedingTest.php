<?php

declare(strict_types=1);

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\MarkdownFaker;
use Awcodes\ContentFaker\RichEditorFaker;
use Faker\Factory;
use Faker\Provider\Base;

dataset('fakers', [
    'markdown' => MarkdownFaker::class,
    'html' => HtmlFaker::class,
    'rich editor' => RichEditorFaker::class,
]);

/** @param  class-string<MarkdownFaker|HtmlFaker>  $class */
function seededContent(string $class, int $seed): string
{
    $faker = Factory::create();
    $faker->seed($seed);

    return $class::make($faker)->inlineProbability(100)->random(12)->render();
}

it('produces identical output, decorations included, for the same seed', function (string $class): void {
    expect(seededContent($class, 1234))->toBe(seededContent($class, 1234));
})->with('fakers');

it('produces different output for different seeds', function (string $class): void {
    expect(seededContent($class, 1234))->not->toBe(seededContent($class, 5678));
})->with('fakers');

it('uses a generator passed to withFaker', function (string $class): void {
    $faker = Factory::create();
    $faker->addProvider(new class($faker) extends Base
    {
        public function sentence($nbWords = 6, $variableNbWords = true): string
        {
            return 'Injected generator sentence.';
        }

        public function paragraph($nbSentences = 3, $variableNbSentences = true): string
        {
            return 'Injected generator paragraph.';
        }
    });

    expect($class::make()->withFaker($faker)->withoutInlineDecorations()->paragraph()->render())
        ->toContain('Injected generator paragraph.')
        ->and($class::make($faker)->withoutInlineDecorations()->paragraph()->render())
        ->toContain('Injected generator paragraph.');
})->with('fakers');

it('is reproducible when seeding the global fake() generator', function (string $class): void {
    fake()->seed(42);
    $first = $class::make()->inlineProbability(100)->random(12)->render();

    fake()->seed(42);
    $second = $class::make()->inlineProbability(100)->random(12)->render();

    expect($first)->toBe($second);
})->with('fakers');

it('passes a generator through the helpers', function (): void {
    $render = function (callable $helper): string {
        $faker = Factory::create();
        $faker->seed(7);

        return $helper($faker)->inlineProbability(100)->paragraphs(3)->render();
    };

    expect($render(markdown_faker(...)))->toBe($render(markdown_faker(...)))
        ->and($render(html_faker(...)))->toBe($render(html_faker(...)))
        ->and($render(rich_editor_faker(...)))->toBe($render(rich_editor_faker(...)));
});
