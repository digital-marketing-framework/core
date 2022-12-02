<?php

namespace DigitalMarketingFramework\Core\Queue;

interface QueueProcessorInterface
{
    public function processJobs(array $jobs): void;
    public function processBatch(int $batchSize = 1): void;
    public function processAll(): void;
}
