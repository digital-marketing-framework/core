<?php

namespace DigitalMarketingFramework\Core\Model\Queue;

use DateTime;

interface JobInterface
{
    public function getId(): int;
    public function setId(int $id);

    public function getCreated(): DateTime;
    public function setCreated(DateTime $created);

    public function getStatus(): int;
    public function setStatus(int $status);

    public function getSkipped(): bool;
    public function setSkipped(bool $skipped);

    public function getStatusMessage(): string;
    public function setStatusMessage(string $message);

    public function getChanged(): DateTime;
    public function setChanged(DateTime $changed);

    public function getData(): array;
    public function setData(array $data);

    public function getHash(): string;
    public function setHash(string $hash);

    public function getLabel(): string;
    public function setLabel(string $label);
}
