<?php

declare(strict_types=1);

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\MarkdownFaker;
use Awcodes\ContentFaker\RichEditorFaker;
use Faker\Generator;

if (! function_exists('markdown_faker')) {
    function markdown_faker(?Generator $faker = null): MarkdownFaker
    {
        return MarkdownFaker::make($faker);
    }
}

if (! function_exists('html_faker')) {
    function html_faker(?Generator $faker = null): HtmlFaker
    {
        return HtmlFaker::make($faker);
    }
}

if (! function_exists('rich_editor_faker')) {
    function rich_editor_faker(?Generator $faker = null): RichEditorFaker
    {
        return RichEditorFaker::make($faker);
    }
}
