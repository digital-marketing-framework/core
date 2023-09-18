<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

class FieldTracker implements FieldTrackerInterface
{
    /** @var array<string,bool> */
    protected array $processedFields = [];

    public function markAsProcessed(string $key): void
    {
        $this->processedFields[$key] = true;
    }

    public function markAsUnprocessed(string $key): void
    {
        if (array_key_exists($key, $this->processedFields)) {
            unset($this->processedFields[$key]);
        }
    }

    public function hasBeenProcessed(string $key): bool
    {
        return $this->processedFields[$key] ?? false;
    }

    public function reset(): void
    {
        $this->processedFields = [];
    }

    public function getProcessedFields(): array
    {
        return array_keys($this->processedFields);
    }
}
