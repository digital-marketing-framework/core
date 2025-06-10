<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Queue\QueueInterface;

/**
 * @implements JobInterface<int>
 */
class Job implements JobInterface
{
    protected ?int $id = null;

    /**
     * @param array<mixed> $data
     */
    public function __construct(
        protected string $environment = '',
        protected DateTime $created = new DateTime(),
        protected DateTime $changed = new DateTime(),
        protected int $status = QueueInterface::STATUS_QUEUED,
        protected bool $skipped = false,
        protected string $statusMessage = '',
        protected array $data = [],
        protected string $hash = '',
        protected string $label = '',
        protected string $type = '',
        protected int $retryAmount = 0,
    ) {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getSkipped(): bool
    {
        return $this->skipped;
    }

    public function setSkipped(bool $skipped): void
    {
        $this->skipped = $skipped;
    }

    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(string $message): void
    {
        $this->statusMessage = $message;
    }

    public function addStatusMessage(string $message): void
    {
        if ($message === '') {
            return;
        }

        $statusMessage = $this->getStatusMessage();
        if ($statusMessage !== '') {
            $statusMessage .= PHP_EOL . PHP_EOL;
        }

        $now = new DateTime();
        $statusMessage .= $now->format('Y-m-d H:i:s: ') . $message;

        $this->setStatusMessage($statusMessage);
    }

    public function getChanged(): DateTime
    {
        return $this->changed;
    }

    public function setChanged(DateTime $changed): void
    {
        $this->changed = $changed;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
        $labelParts = explode('#', $label);
        if (count($labelParts) > 1) {
            array_shift($labelParts);
            $this->setType(implode('#', $labelParts));
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getRetryAmount(): int
    {
        return $this->retryAmount;
    }

    public function setRetryAmount(int $amount): void
    {
        $this->retryAmount = $amount;
    }
}
