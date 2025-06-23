<?php

namespace DigitalMarketingFramework\Core\TestCase;

interface TestCaseManagerAwareInterface
{
    public function setTestCaseManager(TestCaseManagerInterface $testCaseManager): void;
}
