<?php

namespace Awcodes\ContentFaker;

class RichEditorFaker extends HtmlFaker
{
    protected array $mergeTags = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'company_name',
        'unsubscribe_url',
        'app_name',
    ];

    protected string $buttonClass = 'button';

    protected string $columnsClass = 'columns';

    protected string $calloutClass = 'callout';

    protected string $filamentBlockWrapperClass = 'filament-block';

    public function __construct()
    {
        parent::__construct();

        $this->mergeTags = (array) $this->configValue('content-faker.rich_editor.merge_tags', $this->mergeTags);
        $this->buttonClass = (string) $this->configValue('content-faker.rich_editor.button_class', $this->buttonClass);
        $this->columnsClass = (string) $this->configValue('content-faker.rich_editor.columns_class', $this->columnsClass);
        $this->calloutClass = (string) $this->configValue('content-faker.rich_editor.callout_class', $this->calloutClass);
        $this->filamentBlockWrapperClass = (string) $this->configValue('content-faker.rich_editor.filament_block_wrapper_class', $this->filamentBlockWrapperClass);
    }

    public function lead(?string $text = null): static
    {
        $text ??= $this->faker()->sentence(12);

        $this->blocks[] = '<p class="lead">' . $this->renderText($text) . '</p>';

        return $this;
    }

    public function small(?string $text = null): static
    {
        $text ??= $this->faker()->sentence();

        $this->blocks[] = '<p><small>' . $this->renderText($text) . '</small></p>';

        return $this;
    }

    public function button(?string $label = null, ?string $url = null): static
    {
        $label ??= $this->faker()->words(2, true);
        $url ??= '#';

        $this->blocks[] = '<a href="' . $this->e($url) . '" class="' . $this->e($this->buttonClass) . '">' . $this->e($label) . '</a>';

        return $this;
    }

    public function buttonGroup(array|int $buttons = 2): static
    {
        if (is_int($buttons)) {
            $buttons = collect(range(1, max(1, $buttons)))
                ->map(fn (): array => ['label' => $this->faker()->words(2, true), 'url' => '#'])
                ->all();
        }

        $rendered = collect($buttons)
            ->map(function (array $button): string {
                $label = $button['label'] ?? $this->faker()->words(2, true);
                $url = $button['url'] ?? '#';

                return '    <a href="' . $this->e($url) . '" class="' . $this->e($this->buttonClass) . '">' . $this->e($label) . '</a>';
            })
            ->implode("\n");

        $this->blocks[] = "<div class=\"button-group\">\n{$rendered}\n</div>";

        return $this;
    }

    public function callout(?string $text = null, string $type = 'tip'): static
    {
        $type = mb_strtolower($type);

        if (! in_array($type, $this->alertTypes, true)) {
            $type = 'note';
        }

        $text ??= $this->faker()->paragraph();

        $class = $this->e($this->calloutClass);

        $this->blocks[] = <<<HTML
            <div class="{$class} {$class}-{$this->e($type)}" role="note">
                <p>{$this->renderText($text)}</p>
            </div>
            HTML;

        return $this;
    }

    public function columns(array|int $columns = 2): static
    {
        if (is_int($columns)) {
            $count = max(1, $columns);
            $columns = collect(range(1, $count))
                ->map(fn (): string => $this->faker()->paragraph())
                ->all();
        }

        $count = count($columns);

        $rendered = collect($columns)
            ->map(fn (string $content): string => "    <div>\n        <p>" . $this->renderText($content) . "</p>\n    </div>")
            ->implode("\n");

        $class = $this->e($this->columnsClass);

        $this->blocks[] = "<div class=\"{$class} {$class}-{$count}\">\n{$rendered}\n</div>";

        return $this;
    }

    public function customPlaceholder(string $name, array $data = []): static
    {
        $config = $this->e(json_encode($data, JSON_THROW_ON_ERROR));

        $this->blocks[] = <<<HTML
            <div data-placeholder="{$this->e($name)}" data-config="{$config}">
                Placeholder: {$this->e($name)}
            </div>
            HTML;

        return $this;
    }

    public function mergeTag(string $key, ?string $fallback = null): static
    {
        $this->blocks[] = '<p>' . $this->renderMergeTag($key, $fallback) . '</p>';

        return $this;
    }

    public function paragraphWithMergeTags(array $tags = []): static
    {
        if ($tags === []) {
            $this->blocks[] = '<p>Hello ' . $this->renderMergeTag('first_name')
                . ', welcome to ' . $this->renderMergeTag('company_name') . '.</p>';

            return $this;
        }

        $rendered = collect($tags)
            ->map(fn (string $tag): string => $this->renderMergeTag($tag))
            ->implode(' ');

        $this->blocks[] = '<p>' . $this->e($this->faker()->sentence()) . ' ' . $rendered . '</p>';

        return $this;
    }

    public function headingWithMergeTags(int $level = 2, array $tags = []): static
    {
        $level = max(1, min(6, $level));

        if ($tags === []) {
            $tags = ['first_name'];
        }

        $rendered = collect($tags)
            ->map(fn (string $tag): string => $this->renderMergeTag($tag))
            ->implode(' ');

        $this->blocks[] = "<h{$level}>Welcome, {$rendered}</h{$level}>";

        return $this;
    }

    public function filamentBlock(string $type, array $data = []): static
    {
        $heading = $data['heading'] ?? 'Hero';
        $subheading = $data['subheading'] ?? null;
        $content = $data['content'] ?? 'Generated block content.';

        $reserved = ['heading', 'subheading', 'content'];

        $attributes = collect($data)
            ->reject(fn ($value, string $key): bool => in_array($key, $reserved, true))
            ->filter(fn ($value): bool => is_scalar($value) || $value === null)
            ->map(fn ($value, string $key): string => ' data-' . $this->e($key) . '="' . $this->e((string) $value) . '"')
            ->implode('');

        $wrapper = $this->e($this->filamentBlockWrapperClass);
        $type = $this->e($type);

        $inner = "    <h2>{$this->e($heading)}</h2>";

        if ($subheading !== null) {
            $inner .= "\n    <p>{$this->e($subheading)}</p>";
        }

        $inner .= "\n    <p>{$this->e($content)}</p>";

        $this->blocks[] = <<<HTML
            <!-- filament-block: {$type} -->
            <div class="{$wrapper}" data-block="filament" data-type="{$type}"{$attributes}>
            {$inner}
            </div>
            HTML;

        return $this;
    }

    public function filamentBlocks(array $blocks): static
    {
        foreach ($blocks as $block) {
            $this->filamentBlock($block['type'] ?? 'block', $block['data'] ?? []);
        }

        return $this;
    }

    public function article(): static
    {
        return $this
            ->h1($this->faker()->sentence(5))
            ->lead()
            ->paragraphs(2)
            ->figure()
            ->h2($this->faker()->sentence(3))
            ->paragraph()
            ->callout($this->faker()->sentence(), 'tip')
            ->button('Read More', '#')
            ->h2('Summary')
            ->unorderedList();
    }

    public function docsPage(): static
    {
        return $this
            ->h1($this->faker()->sentence(4))
            ->lead()
            ->paragraphWithMergeTags()
            ->h2('Getting Started')
            ->paragraph()
            ->codeBlock('composer require vendor/package', 'bash')
            ->callout($this->faker()->sentence(), 'note')
            ->columns(2)
            ->h2('Next Steps')
            ->buttonGroup(2);
    }

    protected function renderMergeTag(string $key, ?string $fallback = null): string
    {
        if (! preg_match('/^[A-Za-z0-9_.\-]+$/', $key)) {
            $key = 'value';
        }

        if ($fallback === null || $fallback === '') {
            return "{{ {$key} }}";
        }

        $fallback = $this->e($fallback);

        return "{{ {$key}|{$fallback} }}";
    }
}
