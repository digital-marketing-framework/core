<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;

interface JobInterface
{
    public function getId(): int;
    public function setId(int $id): void;

    public function getCreated(): DateTime;
    public function setCreated(DateTime $created): void;

    public function getStatus(): int;
    public function setStatus(int $status): void;

    public function getSkipped(): bool;
    public function setSkipped(bool $skipped): void;

    public function getStatusMessage(): string;
    public function setStatusMessage(string $message): void;

    public function getChanged(): DateTime;
    public function setChanged(DateTime $changed): void;

    public function getData(): array;
    public function setData(array $data): void;

    public function getHash(): string;
    public function setHash(string $hash): void;

    public function getLabel(): string;
    public function setLabel(string $label): void;
}
