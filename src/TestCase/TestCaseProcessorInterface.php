<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface TestCaseProcessorInterface extends PluginInterface
{
    public function processInput(array $input): array;
    public function calculateHash(array $input): string;
}
