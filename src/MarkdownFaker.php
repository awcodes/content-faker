<?php

declare(strict_types=1);

namespace Awcodes\ContentFaker;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Str;
use Stringable;
use Throwable;

/**
 * @phpstan-consistent-constructor
 */
class MarkdownFaker implements Stringable
{
    /** @var array<int, string> */
    protected array $blocks = [];

    protected bool $inlineDecorations = true;

    protected int $inlineProbability = 14;

    protected int $linkProbability = 4;

    protected int $codeProbability = 4;

    protected int $maxInlineDecorationsPerParagraph = 3;

    /** @var array<int, string> */
    protected array $inlineTypes = [
        'bold',
        'italic',
        'bold_italic',
        'strikethrough',
        'code',
        'link',
    ];

    /** @var array<int, string> */
    protected array $alertTypes = [
        'NOTE',
        'TIP',
        'IMPORTANT',
        'WARNING',
        'CAUTION',
    ];

    protected ?Generator $faker = null;

    public function __construct()
    {
        $this->inlineDecorations = (bool) $this->configValue('content-faker.inline_decorations', $this->inlineDecorations);
        $this->inlineProbability = (int) $this->configValue('content-faker.inline_probability', $this->inlineProbability);
        $this->linkProbability = (int) $this->configValue('content-faker.link_probability', $this->linkProbability);
        $this->codeProbability = (int) $this->configValue('content-faker.code_probability', $this->codeProbability);
        $this->maxInlineDecorationsPerParagraph = (int) $this->configValue('content-faker.max_inline_decorations_per_paragraph', $this->maxInlineDecorationsPerParagraph);
        $this->inlineTypes = (array) $this->configValue('content-faker.markdown.inline_types', $this->inlineTypes);
        $this->alertTypes = (array) $this->configValue('content-faker.markdown.alert_types', $this->alertTypes);
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
            'bold',
            'italic',
            'bold_italic',
            'strikethrough',
            'code',
            'link',
        ]));

        return $this;
    }

    public function heading(string $text, int $level = 2): static
    {
        $level = max(1, min(6, $level));

        $this->blocks[] = str_repeat('#', $level) . ' ' . $text;

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

        $this->blocks[] = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        return $this;
    }

    public function paragraphs(int $count = 3): static
    {
        foreach ($this->faker()->paragraphs(max(1, $count)) as $paragraph) {
            $this->paragraph($paragraph);
        }

        return $this;
    }

    public function bold(string $text): static
    {
        $this->blocks[] = "**{$text}**";

        return $this;
    }

    public function italic(string $text): static
    {
        $this->blocks[] = "*{$text}*";

        return $this;
    }

    public function boldItalic(string $text): static
    {
        $this->blocks[] = "***{$text}***";

        return $this;
    }

    public function strikethrough(string $text): static
    {
        $this->blocks[] = "~~{$text}~~";

        return $this;
    }

    public function inlineCode(string $code): static
    {
        $this->blocks[] = "`{$code}`";

        return $this;
    }

    public function link(?string $label = null, ?string $url = null): static
    {
        $label ??= $this->faker()->words(2, true);
        $url ??= $this->faker()->url();

        $this->blocks[] = "[{$label}]({$url})";

        return $this;
    }

    public function image(?string $alt = null, ?string $url = null): static
    {
        $alt ??= $this->faker()->words(3, true);
        $url ??= 'https://picsum.photos/seed/' . Str::slug($alt) . '/1200/800';

        $this->blocks[] = "![{$alt}]({$url})";

        return $this;
    }

    public function blockquote(?string $text = null): static
    {
        $text ??= $this->faker()->paragraph();

        $text = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        $this->blocks[] = collect(preg_split('/\R/', $text))
            ->map(fn (string $line): string => "> {$line}")
            ->implode("\n");

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function unorderedList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->map(fn (string $item): string => '- ' . $this->maybeDecorateListItem($item))
            ->implode("\n");

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function orderedList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->values()
            ->map(fn (string $item, int $index): string => ($index + 1) . '. ' . $this->maybeDecorateListItem($item))
            ->implode("\n");

        return $this;
    }

    /** @param  array<int, string>|int  $items */
    public function taskList(array | int $items = 3): static
    {
        $items = is_int($items)
            ? $this->faker()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->map(fn (string $item): string => '- [' . $this->faker()->randomElement([' ', 'x']) . '] ' . $this->maybeDecorateListItem($item))
            ->implode("\n");

        return $this;
    }

    public function codeBlock(?string $code = null, string $language = 'php'): static
    {
        $code ??= $this->fakeCodeBlock($language);

        $this->blocks[] = "```{$language}\n{$code}\n```";

        return $this;
    }

    public function horizontalRule(): static
    {
        $this->blocks[] = '---';

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
                [$this->faker()->word(), $this->faker()->numberBetween(1, 100)],
                [$this->faker()->word(), $this->faker()->numberBetween(1, 100)],
                [$this->faker()->word(), $this->faker()->numberBetween(1, 100)],
            ];
        }

        $this->blocks[] = collect([
            '| ' . implode(' | ', $headers) . ' |',
            '| ' . collect($headers)->map(fn (): string => '---')->implode(' | ') . ' |',
            ...collect($rows)
                ->map(fn (array $row): string => '| ' . implode(' | ', $row) . ' |')
                ->all(),
        ])->implode("\n");

        return $this;
    }

    public function footnote(?string $label = null, ?string $content = null): static
    {
        $label ??= 'note';
        $content ??= $this->faker()->sentence();

        $this->blocks[] = "Here is a sentence with a footnote.[^{$label}]\n\n[^{$label}]: {$content}";

        return $this;
    }

    public function alert(?string $text = null, string $type = 'TIP'): static
    {
        $type = mb_strtoupper($type);

        if (! in_array($type, $this->alertTypes, true)) {
            $type = 'NOTE';
        }

        $text ??= $this->faker()->paragraph();

        $text = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        $lines = collect(preg_split('/\R/', $text))
            ->map(fn (string $line): string => "> {$line}")
            ->implode("\n");

        $this->blocks[] = "> [!{$type}]\n{$lines}";

        return $this;
    }

    public function callout(?string $text = null, string $type = 'NOTE'): static
    {
        return $this->alert($text, $type);
    }

    public function details(?string $summary = null, ?string $content = null): static
    {
        $summary ??= $this->faker()->sentence(4);
        $content ??= $this->faker()->paragraph();

        $this->blocks[] = <<<MD
            <details>
            <summary>{$summary}</summary>

            {$content}

            </details>
            MD;

        return $this;
    }

    /** @param  array<string, mixed>  $data */
    public function frontMatter(array $data = []): static
    {
        $data = $data ?: [
            'title' => $this->faker()->sentence(4),
            'description' => $this->faker()->sentence(),
            'published' => $this->faker()->boolean(),
        ];

        $yaml = collect($data)
            ->map(fn ($value, string $key): string => "{$key}: " . json_encode($value))
            ->implode("\n");

        array_unshift($this->blocks, "---\n{$yaml}\n---");

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
            ->image()
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
            ->image()
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
            ->alert($this->faker()->sentence(), 'NOTE')
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
            ->alert('This configuration can be published and customized per project.', 'TIP')
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

    protected function decorateInline(string $text): string
    {
        if ($this->maxInlineDecorationsPerParagraph === 0) {
            return $text;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $text) ?: [$text];

        $decorations = 0;

        foreach ($sentences as &$sentence) {
            if ($decorations >= $this->maxInlineDecorationsPerParagraph) {
                break;
            }

            if (! $this->faker()->boolean($this->inlineProbability)) {
                continue;
            }

            $decorated = $this->decorateSentence($sentence);

            if ($decorated !== $sentence) {
                $sentence = $decorated;
                $decorations++;
            }
        }

        return implode(' ', $sentences);
    }

    protected function decorateSentence(string $sentence): string
    {
        $words = preg_split('/\s+/', $sentence);

        if (! $words || count($words) < 3) {
            return $sentence;
        }

        $start = random_int(0, count($words) - 1);
        $length = random_int(1, min(3, count($words) - $start));

        $phrase = implode(' ', array_slice($words, $start, $length));

        if (! $this->isSafeInlinePhrase($phrase)) {
            return $sentence;
        }

        $replacement = $this->randomInline($phrase);

        array_splice($words, $start, $length, [$replacement]);

        return implode(' ', $words);
    }

    protected function randomInline(string $text): string
    {
        $text = trim($text);

        if ($this->inlineTypes === []) {
            return $text;
        }

        if ($this->faker()->boolean($this->linkProbability)) {
            return "[{$text}](" . $this->faker()->url() . ')';
        }

        if ($this->faker()->boolean($this->codeProbability)) {
            return '`' . $this->fakeInlineCode() . '`';
        }

        return match ($this->faker()->randomElement($this->inlineTypes)) {
            'bold' => "**{$text}**",
            'italic' => "*{$text}*",
            'bold_italic' => "***{$text}***",
            'strikethrough' => "~~{$text}~~",
            'code' => '`' . $this->fakeInlineCode() . '`',
            'link' => "[{$text}](" . $this->faker()->url() . ')',
            default => $text,
        };
    }

    protected function maybeDecorateListItem(string $item): string
    {
        if (! $this->inlineDecorations) {
            return $item;
        }

        if (! $this->faker()->boolean($this->inlineProbability)) {
            return $item;
        }

        return $this->decorateInline($item);
    }

    protected function isSafeInlinePhrase(string $text): bool
    {
        if (trim($text) === '') {
            return false;
        }

        return ! Str::contains($text, [
            '[',
            ']',
            '(',
            ')',
            '`',
            '*',
            '_',
            '~',
            '#',
            '|',
            '<',
            '>',
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
                    return 'Hello markdown';
                }
                JS,

            'php' => <<<'PHP'
                public function example(): string
                {
                    return 'Hello markdown';
                }
                PHP,

            default => $this->faker()->paragraph(),
        };
    }
}
