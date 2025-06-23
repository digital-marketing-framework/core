<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;

interface TestCaseManagerInterface
{
    public function runTest(TestCaseInterface $test): TestResult;

    /**
     * @param array<TestCaseInterface> $tests
     *
     * @return array<TestResult>
     */
    public function runTests(array $tests, bool $triggerNotification = false, bool $ignoreOutdated = false): array;

    /**
     * @return array<TestResult>
     */
    public function runAllTests(bool $triggerNotification = false, bool $ignoreOutdated = false): array;

    public function updateHash(TestCaseInterface $test): void;

    /**
     * @param array<TestCaseInterface> $tests
     */
    public function updateHashes(array $tests): void;

    public function getTestCaseStorage(): TestCaseStorageInterface;
}
