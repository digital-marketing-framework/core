<?php

namespace DigitalMarketingFramework\Core\TestCase;

/**
 * @phpstan-ignore-next-line This trait can be used by other packages, even though it is not used in this one.
 */
trait TestCaseManagerAwareTrait
{
    protected TestCaseManagerInterface $testCaseManager;

    public function setTestCaseManager(TestCaseManagerInterface $testCaseManager): void
    {
        $this->testCaseManager = $testCaseManager;
    }
}
