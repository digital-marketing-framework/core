<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Backend\ItemInterface;

interface JobInterface extends ItemInterface
{
    public function getId(): ?int;

    public function setId(int $id): void;

    public function getEnvironment(): string;

    public function setEnvironment(string $environment): void;

    public function getCreated(): DateTime;

    public function setCreated(DateTime $created): void;

    public function getStatus(): int;

    public function setStatus(int $status): void;

    public function getSkipped(): bool;

    public function setSkipped(bool $skipped): void;

    public function getStatusMessage(): string;

    public function setStatusMessage(string $message): void;

    public function addStatusMessage(string $message): void;

    public function getChanged(): DateTime;

    public function setChanged(DateTime $changed): void;

    /**
     * @return array<mixed>
     */
    public function getData(): array;

    /**
     * @param array<mixed> $data
     */
    public function setData(array $data): void;

    public function getHash(): string;

    public function setHash(string $hash): void;

    public function getLabel(): string;

    public function setLabel(string $label): void;

    public function getType(): string;

    public function setType(string $type): void;

    public function getRetryAmount(): int;

    public function setRetryAmount(int $amount): void;
}
