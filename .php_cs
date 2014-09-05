<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/module')
;

return Symfony\CS\Config\Config::create()
    ->fixers(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder)
;

