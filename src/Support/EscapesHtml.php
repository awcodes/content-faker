<?php

declare(strict_types=1);

namespace Awcodes\ContentFaker\Support;

trait EscapesHtml
{
    protected function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
