<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Plugin\PluginInterface;

interface TestCaseProcessorInterface extends PluginInterface
{
    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function processInput(array $input): array;

    /**
     * @param array<string,mixed> $input
     */
    public function calculateHash(array $input): string;
}
