<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__ . '/src',
            // The Testbench workbench is hand-written code that exercises this
            // package's public API, so it is refactored like anything else.
            __DIR__ . '/workbench',
            __DIR__ . '/config',
        ])
        ->withSkip([
            // Gitignored symlink into testbench's storage dir; fills with compiled
            // Blade once the workbench app runs. withSkip() tolerates a path that
            // does not exist, unlike withPaths().
            __DIR__ . '/workbench/storage',
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            typeDeclarations: true,
            privatization: true,
            earlyReturn: true,
        )
        ->withPhpSets();
} catch (Rector\Exception\Configuration\InvalidConfigurationException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
