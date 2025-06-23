<?php

namespace DigitalMarketingFramework\Core\TestCase;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Model\ItemInterface;
use DigitalMarketingFramework\Core\Model\TestCase\TestCaseInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;

class TestResult implements ItemInterface
{
    public const STATUS_SUCCESS = 1;

    public const STATUS_FAIL = 2;

    public const STATUS_ERROR = 3;

    /**
     * @param ?array<string,mixed> $output
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
        if ($this->test->getLabel() !== '' && $this->test->getLabel() !== $this->test->getName()) {
            return sprintf('%s (%s)', $this->test->getLabel(), $this->test->getName());
        }

        return $this->test->getName();
    }

    public function getId(): int|string|null
    {
        return $this->test->getId();
    }

    public function setId(int|string $id): void
    {
        throw new BadMethodCallException('Method "setId" on TestResult not supported');
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function isOutdated(): bool
    {
        return $this->hash !== $this->test->getHash();
    }

    public function getStatus(): int
    {
        if ($this->error !== null) {
            return static::STATUS_ERROR;
        }

        if (!GeneralUtility::compare($this->getOutput(), $this->test->getExpectedOutput())) {
            return static::STATUS_FAIL;
        }

        return static::STATUS_SUCCESS;
    }

    public function getTest(): TestCaseInterface
    {
        return $this->test;
    }

    /**
     * @return ?array<string,mixed>
     */
    public function getOutput(): ?array
    {
        return $this->output;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getIssue(bool $ignoreOutdated = false): ?string
    {
        $label = $this->getLabel();
        $status = $this->getStatus();
        $outdated = $this->isOutdated();

        if ($status === TestResult::STATUS_ERROR) {
            return sprintf('Test case "%s" errored: "%s"', $label, $this->getError());
        }

        if ($status === TestResult::STATUS_FAIL) {
            return sprintf('Test case "%s" failed%s.', $label, $outdated ? ', and is outdated' : '');
        }

        if (!$ignoreOutdated && $outdated) {
            return sprintf('Test case "%s" succeeded but is outdated.', $label);
        }

        return null;
    }
}
