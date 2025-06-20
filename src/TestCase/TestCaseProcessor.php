<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Plugin\Plugin;

abstract class TestCaseProcessor extends Plugin implements TestCaseProcessorInterface
{
    abstract public function processInput(array $input): array;

    abstract public function calculateHash(array $input): string;
}
