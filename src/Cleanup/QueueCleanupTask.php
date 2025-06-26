<?php

namespace DigitalMarketingFramework\Core\Cleanup;

use DigitalMarketingFramework\Core\Queue\QueueProcessorInterface;

abstract class QueueCleanupTask extends CleanupTask
{
    public function __construct(
        string $keyword,
        protected QueueProcessorInterface $queueProcessor,
    ) {
        parent::__construct($keyword);
    }

    public function execute(): void
    {
        $this->queueProcessor->cleanupJobs();
    }
}
