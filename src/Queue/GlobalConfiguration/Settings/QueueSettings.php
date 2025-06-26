<?php

namespace DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Settings;

use DigitalMarketingFramework\Core\GlobalConfiguration\Settings\GlobalSettings;
use DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Schema\QueueSchema;

abstract class QueueSettings extends GlobalSettings
{
    public function __construct(
        protected string $packageName,
        protected string $component = QueueSchema::KEY_QUEUE,
    ) {
        parent::__construct($packageName, $component);
    }

    public function getExpirationTime(): int
    {
        return $this->get(QueueSchema::KEY_QUEUE_EXPIRATION_TIME);
    }

    public function getMaximumExecutionTime(): int
    {
        return $this->get(QueueSchema::KEY_QUEUE_MAXIMUM_EXECUTION_TIME);
    }

    public function recogniseStuckJobs(): bool
    {
        return $this->get(QueueSchema::KEY_QUEUE_RECOGNISE_STUCK_JOBS);
    }

    public function rerunFailedJobEnabled(): bool
    {
        return $this->get(QueueSchema::KEY_QUEUE_RE_RUN_FAILED_JOBS_ENABLED);
    }

    public function getRerunFailedJobAmount(): int
    {
        return $this->get(QueueSchema::KEY_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT);
    }

    public function getRerunFailedJobDelay(): int
    {
        return $this->get(QueueSchema::KEY_QUEUE_RE_RUN_DELAY);
    }

    public function cleanupDoneJobsOnly(): bool
    {
        return $this->get(QueueSchema::KEY_QUEUE_CLEANUP_DONE_JOBS_ONLY);
    }

    public function getBatchSize(): int
    {
        return $this->get(QueueSchema::KEY_QUEUE_PROCESSOR_BATCH_SIZE);
    }
}
