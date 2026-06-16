<?php

use Awcodes\ContentFaker\HtmlFaker;
use Awcodes\ContentFaker\MarkdownFaker;
use Awcodes\ContentFaker\RichEditorFaker;

if (! function_exists('markdown_faker')) {
    function markdown_faker(): MarkdownFaker
    {
        return MarkdownFaker::make();
    }
}

if (! function_exists('html_faker')) {
    function html_faker(): HtmlFaker
    {
        return HtmlFaker::make();
    }
}

if (! function_exists('rich_editor_faker')) {
    function rich_editor_faker(): RichEditorFaker
    {
        return RichEditorFaker::make();
    }
}
