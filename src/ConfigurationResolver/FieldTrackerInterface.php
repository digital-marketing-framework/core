<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver;

interface FieldTrackerInterface
{
    public function markAsProcessed($key);
    public function markAsUnprocessed($key);
    public function hasBeenProcessed($key);
    public function reset();
    public function getProcessedFields();
}
