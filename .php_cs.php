<?php

$finder = PhpCsFixer\Finder::create()
    ->in("src")
    ->in("tests")
;

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
$config->setCacheFile("reports/.php-cs-fixer.cache");
$config->setFormat("txt");
return $config->setRules([
    '@PSR12' => true,
    '@PHP80Migration:risky' => true,
    '@PhpCsFixer' => true,
    'strict_param' => true,
    'declare_strict_types' => true,
    'array_indentation' => true,
    'array_syntax' => ['syntax' => 'short'],
    'php_unit_test_class_requires_covers' => false
])->setFinder($finder);