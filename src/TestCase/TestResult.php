<?php

namespace DigitalMarketingFramework\Core\TestCase;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

/**
 * @implements ItemInterface<int|string>
 */
class TestResult implements ItemInterface
{
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAIL = 2;
    public const STATUS_ERROR = 3;
    public const STATUS_OUTDATED = 4;

    /**
     * @param TestCaseInterface<int|string> $test
     * @param ?array<mixed> $output
     */
    public function __construct(
        protected TestCaseInterface $test,
        protected string $hash = '',
        protected ?array $output = null,
        protected ?string $error = null,
    ) {
    }

    public function getLabel(): string
    {
        return $this->test->getLabel();
    }

    public function getId()
    {
        return $this->test->getId();
    }

    public function setId($id): void
    {
        throw new BadMethodCallException('Method "setId" on TestResult not supported');
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
