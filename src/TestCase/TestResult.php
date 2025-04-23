<?php

namespace DigitalMarketingFramework\Core\TestCase;

use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class TestResult
{
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAIL = 2;
    public const STATUS_ERROR = 3;
    public const STATUS_OUTDATED = 4;

    public function __construct(
        protected TestCaseInterface $test,
        protected string $hash = '',
        protected ?array $output = null,
        protected ?string $error = null,
    ) {
    }

    public function getStatus(): int
    {
        if ($this->error !== null) {
            return static::STATUS_ERROR;
        }

        if (!GeneralUtility::compare($this->getOutput(), $this->test->getExpectedOutput())) {
            return static::STATUS_FAIL;
        }

        if ($this->hash !== $this->test->getHash()) {
            return static::STATUS_OUTDATED;
        }

        return static::STATUS_SUCCESS;
    }

    public function getTest(): TestCaseInterface
    {
        return $this->test;
    }

    public function getOutput(): ?array
    {
        return $this->output;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
