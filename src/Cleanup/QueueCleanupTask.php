<?php

namespace DigitalMarketingFramework\Core\Cleanup;

use DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Settings\QueueSettings;
use DigitalMarketingFramework\Core\Queue\QueueInterface;

abstract class QueueCleanupTask extends CleanupTask
{
    public function __construct(
        string $keyword,
        protected QueueInterface $queue,
    ) {
        parent::__construct($keyword);
    }

    abstract protected function getQueueSettings(): QueueSettings;

    public function execute(): void
    {
        $expirationTime = $this->getQueueSettings()->getExpirationTime();
        $status = $this->getQueueSettings()->cleanupDoneJobsOnly() ? [QueueInterface::STATUS_DONE] : [];

        $this->queue->removeOldJobs($expirationTime, $status);
    }
}
