<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Queue\QueueInterface;

class Job implements JobInterface
{
    protected string $routeId;

    /**
     * @param array<mixed> $data
     */
    public function __construct(
        protected DateTime $created = new DateTime(),
        protected DateTime $changed = new DateTime(),
        protected int $status = QueueInterface::STATUS_QUEUED,
        protected bool $skipped = false,
        protected string $statusMessage = '',
        protected array $data = [],
        protected string $hash = '',
        protected string $label = '',
    ) {
    }

    public function getRouteId(): string
    {
        return $this->routeId;
    }

    public function setRouteId(string $routeId): void
    {
        $this->routeId = $routeId;
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
    }
}
