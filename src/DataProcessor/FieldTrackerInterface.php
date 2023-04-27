<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

interface FieldTrackerInterface
{
    public function markAsProcessed(string $key): void;
    public function markAsUnprocessed(string $key): void;
    public function hasBeenProcessed(string $key): bool;
    public function reset(): void;
    public function getProcessedFields(): array;
}
