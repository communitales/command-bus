<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'ordered_class_elements' => true,
        'ordered_interfaces' => true,
        'protected_to_private' => true,
    ])
    ->setFinder($finder);
