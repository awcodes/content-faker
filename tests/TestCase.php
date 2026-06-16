<?php

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
