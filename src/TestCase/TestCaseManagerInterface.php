<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;

interface TestCaseManagerInterface
{
    public function runTest(TestCaseInterface $test): TestResult;

    /**
     * @param array<TestCaseInterface> $tests
     * @return array<TestResult>
     */
    public function runTests(array $tests): array;

    /**
     * @return array<TestResult>
     */
    public function runAllTests(): array;
}
