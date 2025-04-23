<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;

interface TestCaseStorageInterface
{
    /**
     * @return array<TestCaseInterface>
     */
    public function getAllTestCases(): array;

    /**
     * @return array<TestCaseInterface>
     */
    public function getTypeSpecificTestCases(string $type): array;
}
