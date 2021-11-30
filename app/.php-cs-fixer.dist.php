<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/features'
    ])
;

$config = new PhpCsFixer\Config();
$config
    ->setLineEnding("\r\n")
    ->setRiskyAllowed(true)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
    ])
    ->setFinder($finder)
;

return $config;
