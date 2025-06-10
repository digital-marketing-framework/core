<?php

namespace DigitalMarketingFramework\Core\TestCase;

trait TestCaseManagerAwareTrait
{
    protected TestCaseManagerInterface $testCaseManager;

    public function setTestCaseManager(TestCaseManagerInterface $testCaseManager): void
    {
        $this->testCaseManager = $testCaseManager;
    }
}
