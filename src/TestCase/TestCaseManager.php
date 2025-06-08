<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class TestCaseManager implements TestCaseManagerInterface
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function getTestCaseStorage(): TestCaseStorageInterface
    {
        return $this->registry->getTestCaseStorage();
    }

    public function runTest(TestCaseInterface $test): TestResult
    {
        try {
            $type = $test->getType();
            $processor = $this->registry->getTestCaseProcessor($type);
            if (!$processor instanceof TestCaseProcessorInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('No test processor for type "%s" found.', $type));
            }

            $output = $processor->processInput($test->getInput());
            $hash = $processor->calculateHash($test->getInput());

            return new TestResult($test, $hash, $output);
        } catch (DigitalMarketingFrameworkException $e) {
            return new TestResult($test, error: $e->getMessage());
        }
    }

    public function runTests(array $tests): array
    {
        $results = [];
        foreach ($tests as $test) {
            $results[] = $this->runTest($test);
        }

        return $results;
    }

    public function runAllTests(): array
    {
        $tests = $this->getTestCaseStorage()->getAllTestCases();

        return $this->runTests($tests);
    }
}
