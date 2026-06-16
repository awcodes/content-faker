<?php

declare(strict_types=1);

namespace Awcodes\ContentFaker\Tests;

use Awcodes\ContentFaker\ContentFakerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ContentFakerServiceProvider::class,
        ];
    }
}
