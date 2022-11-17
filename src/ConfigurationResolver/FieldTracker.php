<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver;

class FieldTracker implements FieldTrackerInterface
{
    protected array $processedFields = [];

    public function markAsProcessed($key)
    {
        $this->processedFields[$key] = true;
    }

    public function markAsUnprocessed($key)
    {
        if (array_key_exists($key, $this->processedFields)) {
            unset($this->processedFields[$key]);
        }
    }

    public function hasBeenProcessed($key)
    {
        return $this->processedFields[$key] ?? false;
    }

    public function reset()
    {
        $this->processedFields = [];
    }

    public function getProcessedFields()
    {
        return $this->processedFields;
    }
}
