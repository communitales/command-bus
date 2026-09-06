<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/preload.php',
        'config/reference.php',
        'public/index.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP84Migration' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'ordered_interfaces' => true,
        'protected_to_private' => true,
        'single_quote' => true,
        'header_comment' => [
            'comment_type' => 'comment',
            'location' => 'after_declare_strict',
            'header' => "SPDX-FileCopyrightText: 2020 Communitales GmbH\n\nSPDX-License-Identifier: MIT",
        ],

    ])
    ->setFinder($finder)
    ->setCacheFile('var/cache/.php-cs-fixer.cache')
    ->setParallelConfig(ParallelConfigFactory::detect());
