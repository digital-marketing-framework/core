<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Item;
use DigitalMarketingFramework\Core\Queue\QueueInterface;
use DigitalMarketingFramework\Core\Utility\QueueUtility;
use JsonException;

class Job extends Item implements JobInterface
{
    public function __construct(
        protected string $environment = '',
        protected DateTime $created = new DateTime(),
        protected DateTime $changed = new DateTime(),
        protected int $status = QueueInterface::STATUS_QUEUED,
        protected bool $skipped = false,
        protected string $statusMessage = '',
        protected string $serializedData = '',
        protected string $hash = '',
        protected string $label = '',
        protected string $type = '',
        protected int $retryAmount = 0,
    ) {
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

    /**
     * @return array<string>
     */
    public function getErrorMessages(): array
    {
        if ($this->getStatus() !== QueueInterface::STATUS_FAILED) {
            return [];
        }

        return array_map(fn (array $error) => $error['message'], QueueUtility::getErrors($this));
    }

    public function getLatestErrorMessage(): ?string
    {
        $errors = $this->getErrorMessages();

        return array_pop($errors);
    }

    public function getChanged(): DateTime
    {
        return $this->changed;
    }

    public function setChanged(DateTime $changed): void
    {
        $this->changed = $changed;
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

    public function getSerializedData(): string
    {
        return $this->serializedData;
    }

    public function setSerializedData(string $serializedData): void
    {
        $this->serializedData = $serializedData;
    }

    public function getData(): array
    {
        $data = $this->getSerializedData();
        if ($data === '') {
            return [];
        }

        try {
            return json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }
    }

    public function setData(array $data): void
    {
        try {
            $serializedData = json_encode($data, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->setStatus(QueueInterface::STATUS_FAILED);
            $this->setStatusMessage(sprintf('data encoding failed [%d]: "%s"', $e->getCode(), $e->getMessage()));
            try {
                $serializedData = json_encode($data, flags: JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                $serializedData = print_r($data, true);
            }
        }

        $this->setSerializedData($serializedData);
    }
}
