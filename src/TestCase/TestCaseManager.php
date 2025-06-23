<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareTrait;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class TestCaseManager implements TestCaseManagerInterface, NotificationManagerAwareInterface
{
    use NotificationManagerAwareTrait;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function getTestCaseStorage(): TestCaseStorageInterface
    {
        return $this->registry->getTestCaseStorage();
    }

    protected function getTestCaseProcessor(TestCaseInterface $test): TestCaseProcessorInterface
    {
        $type = $test->getType();
        $processor = $this->registry->getTestCaseProcessor($type);
        if (!$processor instanceof TestCaseProcessorInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No test processor for type "%s" found.', $type));
        }

        return $processor;
    }

    public function updateHash(TestCaseInterface $test): void
    {
        $processor = $this->getTestCaseProcessor($test);
        $hash = $processor->calculateHash($test->getInput());
        $test->setHash($hash);
        $this->getTestCaseStorage()->update($test);
    }

    public function updateHashes(array $tests): void
    {
        foreach ($tests as $test) {
            $this->updateHash($test);
        }
    }

    /**
     * @param array<TestResult> $results
     *
     * @return array<string>
     */
    protected function findIssues(array $results, bool $ignoreOutdated = false): array
    {
        $issues = [];
        foreach ($results as $result) {
            $report = $result->getIssue($ignoreOutdated);
            if ($report !== null) {
                $issues[] = $report;
            }
        }

        return $issues;
    }

    public function runTest(TestCaseInterface $test): TestResult
    {
        try {
            $processor = $this->getTestCaseProcessor($test);
            $output = $processor->processInput($test->getInput());
            $hash = $processor->calculateHash($test->getInput());

            return new TestResult($test, $hash, $output);
        } catch (DigitalMarketingFrameworkException $e) {
            return new TestResult($test, error: $e->getMessage());
        }
    }

    public function runTests(array $tests, bool $triggerNotification = false, bool $ignoreOutdated = false): array
    {
        $results = [];
        foreach ($tests as $test) {
            $results[] = $this->runTest($test);
        }

        if ($triggerNotification) {
            $issues = $this->findIssues($results, $ignoreOutdated);
            $issueCount = count($issues);
            if ($issueCount > 0) {
                $message = sprintf('Issues found in %d test cases.%s', $issueCount, PHP_EOL)
                    . implode(PHP_EOL, $issues);

                $this->notificationManager->notify(
                    'Anyrel tests failed',
                    $message,
                    component: 'test-suite',
                    level: NotificationManagerInterface::LEVEL_ERROR
                );
            }
        }

        return $results;
    }

    public function runAllTests(bool $triggerNotification = false, bool $ignoreOutdated = false): array
    {
        $tests = $this->getTestCaseStorage()->fetchAll();

        return $this->runTests($tests, $triggerNotification, $ignoreOutdated);
    }
}
