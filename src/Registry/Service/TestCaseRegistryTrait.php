<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\Plugin\PluginRegistryTrait;
use DigitalMarketingFramework\Core\TestCase\TestCaseManager;
use DigitalMarketingFramework\Core\TestCase\TestCaseManagerInterface;
use DigitalMarketingFramework\Core\TestCase\TestCaseProcessorInterface;
use DigitalMarketingFramework\Core\TestCase\TestCaseStorageInterface;

trait TestCaseRegistryTrait
{
    use PluginRegistryTrait;

    protected TestCaseStorageInterface $testCaseStorage;

    protected TestCaseManagerInterface $testCaseManager;

    public function registerTestCaseProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(TestCaseProcessorInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteTestCaseProcessor(string $keyword): void
    {
        $this->deletePlugin($keyword, TestCaseProcessorInterface::class);
    }

    public function getTestCaseProcessor(string $keyword): ?TestCaseProcessorInterface
    {
        return $this->getPlugin($keyword, TestCaseProcessorInterface::class);
    }

    public function getAllTestCaseProcessorTypes(): array
    {
        return array_keys($this->getAllPluginClasses(TestCaseProcessorInterface::class));
    }

    public function getTestCaseStorage(): TestCaseStorageInterface
    {
        if (!isset($this->testCaseStorage)) {
            throw new DigitalMarketingFrameworkException('No test case storage defined');
        }

        return $this->testCaseStorage;
    }

    public function setTestCaseStorage(TestCaseStorageInterface $testCaseStorage): void
    {
        $this->testCaseStorage = $testCaseStorage;
    }

    public function getTestCaseManager(): TestCaseManagerInterface
    {
        if (!isset($this->testCaseManager)) {
            $this->testCaseManager = $this->createObject(TestCaseManager::class, [$this]);
        }

        return $this->testCaseManager;
    }

    public function setTestCaseManager(TestCaseManagerInterface $testCaseManager): void
    {
        $this->testCaseManager = $testCaseManager;
    }
}
