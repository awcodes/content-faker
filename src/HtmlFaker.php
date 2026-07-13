<?php

declare(strict_types=1);

namespace Awcodes\ContentFaker;

use Awcodes\ContentFaker\Support\EscapesHtml;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Str;
use Stringable;
use Throwable;

/**
 * @phpstan-consistent-constructor
 */
class HtmlFaker implements Stringable
{
    use EscapesHtml;

    /** @var array<int, string> */
    protected array $blocks = [];

    protected bool $inlineDecorations = true;

    protected int $inlineProbability = 14;

    protected int $linkProbability = 4;

    protected int $codeProbability = 4;

    protected int $maxInlineDecorationsPerParagraph = 3;

    /** @var array<int, string> */
    protected array $inlineTypes = [
        'strong',
        'em',
        'strong_em',
        's',
        'code',
        'link',
    ];

    /** @var array<int, string> */
    protected array $alertTypes = [
        'note',
        'tip',
        'important',
        'warning',
        'caution',
    ];

    protected ?Generator $faker = null;

    public function __construct()
    {
        $this->inlineDecorations = (bool) $this->configValue('content-faker.inline_decorations', $this->inlineDecorations);
        $this->inlineProbability = (int) $this->configValue('content-faker.inline_probability', $this->inlineProbability);
        $this->linkProbability = (int) $this->configValue('content-faker.link_probability', $this->linkProbability);
        $this->codeProbability = (int) $this->configValue('content-faker.code_probability', $this->codeProbability);
        $this->maxInlineDecorationsPerParagraph = (int) $this->configValue('content-faker.max_inline_decorations_per_paragraph', $this->maxInlineDecorationsPerParagraph);
        $this->inlineTypes = (array) $this->configValue('content-faker.html.inline_types', $this->inlineTypes);
        $this->alertTypes = (array) $this->configValue('content-faker.html.alert_types', $this->alertTypes);
    }

    public function __toString(): string
    {
        try {
            return $this->toString();
        } catch (Throwable) {
            return '';
        }
    }

    public static function make(): static
    {
        return new static;
    }

    public function render(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return collect($this->blocks)
            ->filter()
            ->implode("\n\n");
    }

    public function withoutInlineDecorations(): static
    {
        $this->inlineDecorations = false;

        return $this;
    }

    public function withInlineDecorations(bool $condition = true): static
    {
        $this->inlineDecorations = $condition;

        return $this;
    }

    public function inlineProbability(int $percentage): static
    {
        $this->inlineProbability = max(0, min(100, $percentage));

        return $this;
    }

    public function linkProbability(int $percentage): static
    {
        $this->linkProbability = max(0, min(100, $percentage));

        return $this;
    }

    public function codeProbability(int $percentage): static
    {
        $this->codeProbability = max(0, min(100, $percentage));

        return $this;
    }

    public function maxInlineDecorationsPerParagraph(int $count): static
    {
        $this->maxInlineDecorationsPerParagraph = max(0, $count);

        return $this;
    }

    /** @param  array<int, string>  $types */
    public function inlineTypes(array $types): static
    {
        $this->inlineTypes = array_values(array_intersect($types, [
            'strong',
            'em',
            'strong_em',
            's',
            'code',
            'link',
        ]));

        return $this;
    }

    public function heading(string $text, int $level = 2): static
    {
        $level = max(1, min(6, $level));

        $this->blocks[] = "<h{$level}>" . $this->e($text) . "</h{$level}>";

        return $this;
    }

    public function h1(string $text): static
    {
        return $this->heading($text, 1);
    }

    public function h2(string $text): static
    {
        return $this->heading($text, 2);
    }

    public function h3(string $text): static
    {
        return $this->heading($text, 3);
    }

    public function h4(string $text): static
    {
        return $this->heading($text, 4);
    }

    public function h5(string $text): static
    {
        return $this->heading($text, 5);
    }

    public function h6(string $text): static
    {
        return $this->heading($text, 6);
    }

    public function paragraph(?string $text = null): static
    {
        $text ??= $this->faker()->paragraph();

        $this->blocks[] = '<p>' . $this->renderText($text) . '</p>';

        return $this;
    }

    public function paragraphs(int $count = 3): static
    {
        foreach ($this->faker()->paragraphs(max(1, $count)) as $paragraph) {
            $this->paragraph($paragraph);
        }

        return $this;
    }

    public function strong(string $text): static
    {
        $this->blocks[] = '<p><strong>' . $this->e($text) . '</strong></p>';

        return $this;
    }

    public function em(string $text): static
    {
        $this->blocks[] = '<p><em>' . $this->e($text) . '</em></p>';

        return $this;
    }

    public function strongEm(string $text): static
    {
        $this->blocks[] = '<p><strong><em>' . $this->e($text) . '</em></strong></p>';

        return $this;
    }

    public function strikethrough(string $text): static
    {
        $this->blocks[] = '<p><s>' . $this->e($text) . '</s></p>';

        return $this;
    }

    public function inlineCode(string $code): static
    {
        $this->blocks[] = '<p><code>' . $this->e($code) . '</code></p>';

        return $this;
    }

    public function link(?string $label = null, ?string $url = null): static
    {
        $label ??= $this->faker()->words(2, true);
        $url ??= $this->faker()->url();

        $this->blocks[] = '<p><a href="' . $this->e($url) . '">' . $this->e($label) . '</a></p>';

        return $this;
    }

    public function image(?string $alt = null, ?string $url = null): static
    {
        $alt ??= $this->faker()->words(3, true);
        $url ??= 'https://picsum.photos/seed/' . Str::slug($alt) . '/1200/800';

        $this->blocks[] = '<img src="' . $this->e($url) . '" alt="' . $this->e($alt) . '">';

        return $this;
    }

    public function figure(?string $alt = null, ?string $caption = null, ?string $url = null): static
    {
        $alt ??= $this->faker()->words(3, true);
        $caption ??= $this->faker()->sentence();
        $url ??= 'https://picsum.photos/seed/' . Str::slug($alt) . '/1200/800';

        $this->blocks[] = <<<HTML
            <figure>
                <img src="{$this->e($url)}" alt="{$this->e($alt)}">
                <figcaption>{$this->e($caption)}</figcaption>
            </figure>
            HTML;

        return $this;
    }

    public function blockquote(?string $text = null): static
    {
        $text ??= $this->faker()->paragraph();

        $this->blocks[] = '<blockquote>' . "\n    <p>" . $this->renderText($text) . "</p>\n" . '</blockquote>';

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function unorderedList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $rows = collect($items)
            ->map(fn (string $item): string => '    <li>' . $this->decorateListItem($item) . '</li>')
            ->implode("\n");

        $this->blocks[] = "<ul>\n{$rows}\n</ul>";

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function orderedList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $rows = collect($items)
            ->map(fn (string $item): string => '    <li>' . $this->decorateListItem($item) . '</li>')
            ->implode("\n");

        $this->blocks[] = "<ol>\n{$rows}\n</ol>";

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function taskList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $rows = collect($items)
            ->map(function (string $item): string {
                $checked = $this->faker()->boolean() ? ' checked' : '';

                return '    <li><input type="checkbox" disabled' . $checked . '> ' . $this->decorateListItem($item) . '</li>';
            })
            ->implode("\n");

        $this->blocks[] = "<ul class=\"task-list\">\n{$rows}\n</ul>";

        return $this;
    }

    public function codeBlock(?string $code = null, string $language = 'php'): static
    {
        $code ??= $this->fakeCodeBlock($language);

        $this->blocks[] = '<pre><code class="language-' . $this->e($language) . '">' . $this->e($code) . '</code></pre>';

        return $this;
    }

    public function horizontalRule(): static
    {
        $this->blocks[] = '<hr>';

        return $this;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, array<int, string|int>>  $rows
     */
    public function table(array $headers = ['Name', 'Value'], array $rows = []): static
    {
        if ($rows === []) {
            $rows = [
                [$this->faker()->word(), (string) $this->faker()->numberBetween(1, 100)],
                [$this->faker()->word(), (string) $this->faker()->numberBetween(1, 100)],
                [$this->faker()->word(), (string) $this->faker()->numberBetween(1, 100)],
            ];
        }

        $head = collect($headers)
            ->map(fn ($header): string => '            <th>' . $this->e((string) $header) . '</th>')
            ->implode("\n");

        $body = collect($rows)
            ->map(function (array $row): string {
                $cells = collect($row)
                    ->map(fn ($cell): string => '            <td>' . $this->e((string) $cell) . '</td>')
                    ->implode("\n");

                return "        <tr>\n{$cells}\n        </tr>";
            })
            ->implode("\n");

        $this->blocks[] = <<<HTML
            <table>
                <thead>
                    <tr>
            {$head}
                    </tr>
                </thead>
                <tbody>
            {$body}
                </tbody>
            </table>
            HTML;

        return $this;
    }

    public function alert(?string $text = null, string $type = 'tip'): static
    {
        $type = mb_strtolower($type);

        if (! in_array($type, $this->alertTypes, true)) {
            $type = 'note';
        }

        $text ??= $this->faker()->paragraph();

        $this->blocks[] = <<<HTML
            <div class="alert alert-{$this->e($type)}" role="note">
                <p>{$this->renderText($text)}</p>
            </div>
            HTML;

        return $this;
    }

    public function details(?string $summary = null, ?string $content = null): static
    {
        $summary ??= $this->faker()->sentence(4);
        $content ??= $this->faker()->paragraph();

        $this->blocks[] = <<<HTML
            <details>
                <summary>{$this->e($summary)}</summary>
                <p>{$this->renderText($content)}</p>
            </details>
            HTML;

        return $this;
    }

    public function random(int $blocks = 8): static
    {
        collect(range(1, max(1, $blocks)))->each(function (): void {
            $this->faker()->randomElement([
                fn (): static => $this->heading($this->faker()->sentence(3), $this->faker()->numberBetween(2, 4)),
                $this->paragraph(...),
                $this->blockquote(...),
                $this->unorderedList(...),
                $this->orderedList(...),
                $this->taskList(...),
                fn (): static => $this->codeBlock(),
                fn (): static => $this->table(),
                fn (): static => $this->image(),
                fn (): static => $this->figure(),
                fn (): static => $this->alert(),
                fn (): static => $this->details(),
                $this->horizontalRule(...),
            ])();
        });

        return $this;
    }

    public function article(): static
    {
        return $this
            ->h1($this->faker()->sentence(5))
            ->paragraphs(2)
            ->h2($this->faker()->sentence(3))
            ->paragraph()
            ->blockquote()
            ->unorderedList()
            ->h2($this->faker()->sentence(3))
            ->figure()
            ->paragraph()
            ->codeBlock()
            ->h2('Summary')
            ->orderedList();
    }

    public function blogPost(): static
    {
        return $this
            ->h1($this->faker()->sentence(4))
            ->paragraphs(2)
            ->figure()
            ->h2($this->faker()->sentence(4))
            ->blockquote()
            ->paragraph()
            ->codeBlock()
            ->h2('Wrapping Up')
            ->paragraph()
            ->orderedList();
    }

    public function docsPage(): static
    {
        return $this
            ->h1($this->faker()->sentence(4))
            ->paragraph()
            ->alert($this->faker()->sentence(), 'note')
            ->h2('Getting Started')
            ->paragraph()
            ->codeBlock('composer require vendor/package', 'bash')
            ->h2('Usage')
            ->paragraph()
            ->codeBlock(language: 'php')
            ->h2('Options')
            ->table(['Option', 'Default', 'Description'], [
                ['enabled', 'true', 'Whether the feature is enabled.'],
                ['driver', 'default', 'The driver used by the package.'],
            ])
            ->h2('Next Steps')
            ->unorderedList();
    }

    public function technicalDocs(): static
    {
        return $this
            ->h1($this->faker()->sentence(4))
            ->paragraph()
            ->h2('Installation')
            ->codeBlock('composer require vendor/package', 'bash')
            ->h2('Usage')
            ->paragraph()
            ->codeBlock(language: 'php')
            ->h2('Configuration')
            ->table(['Option', 'Default', 'Description'], [
                ['enabled', 'true', 'Whether the feature is enabled.'],
                ['driver', 'default', 'The driver used by the package.'],
                ['cache', 'false', 'Whether generated content should be cached.'],
            ])
            ->alert('This configuration can be published and customized per project.', 'tip')
            ->h2('Next Steps')
            ->orderedList();
    }

    public function releaseNotes(): static
    {
        return $this
            ->h1('Release Notes')
            ->paragraph($this->faker()->sentence())
            ->h2('Added')
            ->unorderedList()
            ->h2('Changed')
            ->unorderedList()
            ->h2('Fixed')
            ->unorderedList()
            ->h2('Upgrade Guide')
            ->paragraph()
            ->codeBlock('composer update vendor/package', 'bash');
    }

    protected function configValue(string $key, mixed $default = null): mixed
    {
        if (function_exists('config') && function_exists('app') && app()->bound('config')) {
            return config($key, $default);
        }

        return $default;
    }

    protected function faker(): Generator
    {
        if ($this->faker instanceof Generator) {
            return $this->faker;
        }

        if (function_exists('fake')) {
            return $this->faker = fake();
        }

        return $this->faker = Factory::create();
    }

    protected function renderText(string $text): string
    {
        return $this->inlineDecorations
            ? $this->decorateInline($text)
            : $this->e($text);
    }

    protected function decorateInline(string $text): string
    {
        if ($this->maxInlineDecorationsPerParagraph === 0) {
            return $this->e($text);
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $text) ?: [$text];

        $decorations = 0;
        $output = [];

        foreach ($sentences as $sentence) {
            if (
                $decorations < $this->maxInlineDecorationsPerParagraph
                && $this->faker()->boolean($this->inlineProbability)
            ) {
                $decorated = $this->decorateSentence($sentence);

                if ($decorated !== null) {
                    $output[] = $decorated;
                    $decorations++;

                    continue;
                }
            }

            $output[] = $this->e($sentence);
        }

        return implode(' ', $output);
    }

    protected function decorateSentence(string $sentence): ?string
    {
        $words = preg_split('/\s+/', $sentence);

        if (! $words || count($words) < 3) {
            return null;
        }

        $start = random_int(0, count($words) - 1);
        $length = random_int(1, min(3, count($words) - $start));

        $phrase = implode(' ', array_slice($words, $start, $length));

        if (! $this->isSafeInlinePhrase($phrase)) {
            return null;
        }

        $replacement = $this->randomInline($phrase);

        $placeholder = "\0DECORATION\0";

        array_splice($words, $start, $length, [$placeholder]);

        return collect($words)
            ->map(fn (string $word): string => $word === $placeholder ? $replacement : $this->e($word))
            ->implode(' ');
    }

    protected function randomInline(string $text): string
    {
        $text = mb_trim($text);

        if ($this->inlineTypes === []) {
            return $this->e($text);
        }

        if ($this->faker()->boolean($this->linkProbability)) {
            return '<a href="' . $this->e($this->faker()->url()) . '">' . $this->e($text) . '</a>';
        }

        if ($this->faker()->boolean($this->codeProbability)) {
            return '<code>' . $this->e($this->fakeInlineCode()) . '</code>';
        }

        return match ($this->faker()->randomElement($this->inlineTypes)) {
            'strong' => '<strong>' . $this->e($text) . '</strong>',
            'em' => '<em>' . $this->e($text) . '</em>',
            'strong_em' => '<strong><em>' . $this->e($text) . '</em></strong>',
            's' => '<s>' . $this->e($text) . '</s>',
            'code' => '<code>' . $this->e($this->fakeInlineCode()) . '</code>',
            'link' => '<a href="' . $this->e($this->faker()->url()) . '">' . $this->e($text) . '</a>',
            default => $this->e($text),
        };
    }

    protected function decorateListItem(string $item): string
    {
        if (! $this->inlineDecorations) {
            return $this->e($item);
        }

        if (! $this->faker()->boolean($this->inlineProbability)) {
            return $this->e($item);
        }

        return $this->decorateInline($item);
    }

    protected function isSafeInlinePhrase(string $text): bool
    {
        if (mb_trim($text) === '') {
            return false;
        }

        return ! Str::contains($text, [
            '<',
            '>',
            '&',
            '"',
            "'",
        ]);
    }

    protected function fakeInlineCode(): string
    {
        return $this->faker()->randomElement([
            'php artisan migrate',
            'php artisan make:model Post',
            'Route::get()',
            'Post::query()',
            '$request->validated()',
            'composer install',
            'npm run build',
            'config()',
            'collect()',
            'Str::slug()',
        ]);
    }

    protected function fakeCodeBlock(string $language): string
    {
        return match ($language) {
            'bash', 'sh' => $this->faker()->randomElement([
                'composer install',
                'php artisan migrate',
                'php artisan make:model Post -mf',
                'npm install && npm run build',
            ]),

            'js', 'javascript' => <<<'JS'
                export default function example() {
                    return 'Hello world';
                }
                JS,

            'php' => <<<'PHP'
                public function example(): string
                {
                    return 'Hello world';
                }
                PHP,

            default => $this->faker()->paragraph(),
        };
    }
}
