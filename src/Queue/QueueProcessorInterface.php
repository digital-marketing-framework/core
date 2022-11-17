<?php

namespace DigitalMarketingFramework\Core\Queue;

interface QueueProcessorInterface
{
    public function processJobs(array $jobs);
    public function processBatch(int $batchSize = 1);
    public function processAll();
}
