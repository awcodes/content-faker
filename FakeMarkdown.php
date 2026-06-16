<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Random\RandomException;
use Stringable;

final class FakeMarkdown implements Stringable
{
    private array $blocks = [];

    private bool $inlineDecorations = true;

    private int $inlineProbability = 14;

    private int $linkProbability = 4;

    private int $codeProbability = 4;

    private int $maxInlineDecorationsPerParagraph = 3;

    private array $inlineTypes = [
        'bold',
        'italic',
        'bold_italic',
        'strikethrough',
        'code',
        'link',
    ];

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function make(): static
    {
        return new self();
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

        $this->blocks[] = str_repeat('#', $level).' '.$text;

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
        $text ??= fake()->paragraph();

        $this->blocks[] = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        return $this;
    }

    public function paragraphs(int $count = 3): static
    {
        foreach (fake()->paragraphs($count) as $paragraph) {
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
        $label ??= fake()->words(2, true);
        $url ??= fake()->url();

        $this->blocks[] = "[{$label}]({$url})";

        return $this;
    }

    public function image(?string $alt = null, ?string $url = null): static
    {
        $alt ??= fake()->words(3, true);
        $url ??= 'https://picsum.photos/seed/'.Str::slug($alt).'/1200/800';

        $this->blocks[] = "![{$alt}]({$url})";

        return $this;
    }

    public function blockquote(?string $text = null): static
    {
        $text ??= fake()->paragraph();

        $text = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        $this->blocks[] = collect(explode("\n", $text))
            ->map(fn (string $line): string => "> {$line}")
            ->implode("\n");

        return $this;
    }

    public function unorderedList(array|int $items = 3): static
    {
        $items = is_int($items)
            ? fake()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->map(fn (string $item): string => '- '.$this->maybeDecorateListItem($item))
            ->implode("\n");

        return $this;
    }

    public function orderedList(array|int $items = 3): static
    {
        $items = is_int($items)
            ? fake()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->values()
            ->map(fn (string $item, int $index): string => ($index + 1).'. '.$this->maybeDecorateListItem($item))
            ->implode("\n");

        return $this;
    }

    public function taskList(array|int $items = 3): static
    {
        $items = is_int($items)
            ? fake()->sentences($items)
            : $items;

        $this->blocks[] = collect($items)
            ->map(fn (string $item): string => '- ['.fake()->randomElement([' ', 'x']).'] '.$this->maybeDecorateListItem($item))
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

    public function table(array $headers = ['Name', 'Value'], array $rows = []): static
    {
        if ($rows === []) {
            $rows = [
                [fake()->word(), fake()->numberBetween(1, 100)],
                [fake()->word(), fake()->numberBetween(1, 100)],
                [fake()->word(), fake()->numberBetween(1, 100)],
            ];
        }

        $this->blocks[] = collect([
            '| '.implode(' | ', $headers).' |',
            '| '.collect($headers)->map(fn (): string => '---')->implode(' | ').' |',
            ...collect($rows)
                ->map(fn (array $row): string => '| '.implode(' | ', $row).' |')
                ->all(),
        ])->implode("\n");

        return $this;
    }

    public function footnote(?string $label = null, ?string $content = null): static
    {
        $label ??= 'note';
        $content ??= fake()->sentence();

        $this->blocks[] = "Here is a sentence with a footnote.[^{$label}]\n\n[^{$label}]: {$content}";

        return $this;
    }

    public function callout(?string $text = null, string $type = 'TIP'): static
    {
        $type = mb_strtoupper($type);

        $allowedTypes = [
            'NOTE',
            'TIP',
            'IMPORTANT',
            'WARNING',
            'CAUTION',
        ];

        if (! in_array($type, $allowedTypes, true)) {
            $type = 'NOTE';
        }

        $text ??= fake()->paragraph();

        $text = $this->inlineDecorations
            ? $this->decorateInline($text)
            : $text;

        $lines = collect(preg_split('/\R/', $text))
            ->map(fn (string $line): string => "> {$line}")
            ->implode("\n");

        $this->blocks[] = "> [!{$type}]\n{$lines}";

        return $this;
    }

    public function details(?string $summary = null, ?string $content = null): static
    {
        $summary ??= fake()->sentence(4);
        $content ??= fake()->paragraph();

        $this->blocks[] = <<<MD
<details>
<summary>{$summary}</summary>

{$content}

</details>
MD;

        return $this;
    }

    public function frontMatter(array $data = []): static
    {
        $data = $data ?: [
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'published' => fake()->boolean(),
        ];

        $yaml = collect($data)
            ->map(fn ($value, string $key): string => "{$key}: ".json_encode($value))
            ->implode("\n");

        array_unshift($this->blocks, "---\n{$yaml}\n---");

        return $this;
    }

    public function random(int $blocks = 8): static
    {
        collect(range(1, $blocks))->each(function (): void {
            fake()->randomElement([
                fn (): FakeMarkdown => $this->heading(fake()->sentence(3), fake()->numberBetween(2, 4)),
                $this->paragraph(...),
                $this->blockquote(...),
                $this->unorderedList(...),
                $this->orderedList(...),
                $this->taskList(...),
                fn (): FakeMarkdown => $this->codeBlock(),
                fn (): FakeMarkdown => $this->table(),
                fn (): FakeMarkdown => $this->image(),
                fn (): FakeMarkdown => $this->callout(),
                fn (): FakeMarkdown => $this->details(),
                $this->horizontalRule(...),
            ])();
        });

        return $this;
    }

    public function article(): static
    {
        return $this
            ->h1(fake()->sentence(5))
            ->paragraphs(2)
            ->h2(fake()->sentence(3))
            ->paragraph()
            ->blockquote()
            ->unorderedList()
            ->h2(fake()->sentence(3))
            ->image()
            ->paragraph()
            ->codeBlock()
            ->h2('Summary')
            ->orderedList();
    }

    public function technicalDocs(): static
    {
        return $this
            ->h1(fake()->sentence(4))
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
            ->callout('This configuration can be published and customized per project.', 'TIP')
            ->h2('Next Steps')
            ->orderedList();
    }

    public function releaseNotes(): static
    {
        return $this
            ->h1('Release Notes')
            ->paragraph(fake()->sentence())
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

    public function blogPost(): static
    {
        return $this
            ->h2(fake()->sentence(4))
            ->paragraphs(2)
            ->image()
            ->h2(fake()->sentence(4))
            ->blockquote()
            ->paragraph()
            ->codeBlock(<<<'PHP'
Post::factory()->create([
    'content' => FakeMarkdown::make()->blogPost()->toString(),
]);
PHP)
            ->h2('Wrapping Up')
            ->paragraph()
            ->orderedList();
    }

    public function laravelArticle(): static
    {
        return $this
            ->h2('Creating the Model')
            ->paragraph()
            ->codeBlock(<<<'PHP'
use App\Models\Post;

$post = Post::query()->create([
    'title' => fake()->sentence(),
    'content' => fake()->paragraph(),
]);
PHP)
            ->h2('Adding Factory Content')
            ->paragraph()
            ->codeBlock(<<<'PHP'
Post::factory()->create([
    'content' => FakeMarkdown::make()->blogPost()->toString(),
]);
PHP)
            ->callout('Factories are a great place to generate realistic markdown content for testing previews, editors, and renderers.', 'TIP')
            ->h2('Summary')
            ->unorderedList([
                'Use factories to create repeatable fake content.',
                'Include headings, lists, code blocks, and inline formatting.',
                'Test your markdown renderer against realistic content.',
            ]);
    }

    public function toString(): string
    {
        return collect($this->blocks)
            ->filter()
            ->implode("\n\n");
    }

    /**
     * @throws RandomException
     */
    private function decorateInline(string $text): string
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

            if (! fake()->boolean($this->inlineProbability)) {
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

    /**
     * @throws RandomException
     */
    private function decorateSentence(string $sentence): string
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

    private function randomInline(string $text): string
    {
        $text = mb_trim($text);

        if (fake()->boolean($this->linkProbability)) {
            return "[{$text}](".fake()->url().')';
        }

        if (fake()->boolean($this->codeProbability)) {
            return '`'.$this->fakeInlineCode().'`';
        }

        return match (fake()->randomElement($this->inlineTypes)) {
            'bold' => "**{$text}**",
            'italic' => "*{$text}*",
            'bold_italic' => "***{$text}***",
            'strikethrough' => "~~{$text}~~",
            'code' => '`'.$this->fakeInlineCode().'`',
            'link' => "[{$text}](".fake()->url().')',
            default => $text,
        };
    }

    private function maybeDecorateListItem(string $item): string
    {
        if (! $this->inlineDecorations) {
            return $item;
        }

        if (! fake()->boolean($this->inlineProbability)) {
            return $item;
        }

        return $this->decorateInline($item);
    }

    private function isSafeInlinePhrase(string $text): bool
    {
        if (mb_trim($text) === '') {
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

    private function fakeInlineCode(): string
    {
        return fake()->randomElement([
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

    private function fakeCodeBlock(string $language): string
    {
        return match ($language) {
            'bash', 'sh' => fake()->randomElement([
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

            default => fake()->paragraph(),
        };
    }
}
