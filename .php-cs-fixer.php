<?php

$config = \Mediatis\CodingStandards\Php\CsFixerSetup::setup();
$rules = $config->getRules();
$rules['protected_to_private'] = false;

return $config->setRules($rules);