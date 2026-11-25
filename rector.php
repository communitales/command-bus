<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 * SPDX-License-Identifier: MIT
 */

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, typeDeclarations: true, phpunitCodeQuality: true, doctrineCodeQuality: true)
    ->withPhpSets(php84: true)
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
    ->withImportNames()
    ->withCache('var/cache/rector')
    ->withParallel(300);
