<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\TestCase\TestCaseManagerInterface;
use DigitalMarketingFramework\Core\TestCase\TestCaseProcessorInterface;
use DigitalMarketingFramework\Core\TestCase\TestCaseStorageInterface;

interface TestCaseRegistryInterface
{
    public function registerTestCaseProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteTestCaseProcessor(string $keyword): void;

    public function getTestCaseProcessor(string $keyword): ?TestCaseProcessorInterface;

    /**
     * @return array<string>
     */
    public function getAllTestCaseProcessorTypes(): array;

    public function getTestCaseStorage(): TestCaseStorageInterface;

    public function setTestCaseStorage(TestCaseStorageInterface $testCaseStorage): void;

    public function getTestCaseManager(): TestCaseManagerInterface;

    public function setTestCaseManager(TestCaseManagerInterface $testCaseManager): void;
}
