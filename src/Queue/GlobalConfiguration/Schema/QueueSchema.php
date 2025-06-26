<?php

namespace DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;

class QueueSchema extends ContainerSchema
{
    public const KEY_QUEUE = 'queue';

    public const KEY_QUEUE_EXPIRATION_TIME = 'expirationTime';

    public const DEFAULT_QUEUE_EXPIRATION_TIME = 30;

    public const KEY_QUEUE_MAXIMUM_EXECUTION_TIME = 'maxExecutionTime';

    public const DEFAULT_QUEUE_MAXIMUM_EXECUTION_TIME = 600;

    public const KEY_QUEUE_RECOGNISE_STUCK_JOBS = 'recogniseStuckJobs';

    public const DEFAULT_QUEUE_RECOGNISE_STUCK_JOBS = false;

    public const KEY_QUEUE_RE_RUN_FAILED_JOBS_ENABLED = 'rerunEnabled';

    public const DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_ENABLED = false;

    public const KEY_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT = 'rerunFailedJobsAmount';

    public const DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT = 3;

    public const KEY_QUEUE_RE_RUN_DELAY = 'rerunDelay';

    public const DEFAULT_QUEUE_RE_RUN_DELAY = 300;

    public const KEY_QUEUE_CLEANUP_DONE_JOBS_ONLY = 'cleanupDoneOnly';

    public const DEFAULT_QUEUE_CLEANUP_DONE_JOBS_ONLY = true;

    public const KEY_QUEUE_PROCESSOR_BATCH_SIZE = 'processorBatchSize';

    public const DEFAULT_QUEUE_PROCESSOR_BATCH_SIZE = 10;

    public function __construct()
    {
        parent::__construct();

        $batchSizeSchema = new IntegerSchema(static::DEFAULT_QUEUE_PROCESSOR_BATCH_SIZE);
        $batchSizeSchema->getRenderingDefinition()->setLabel('Async processing batch size');
        $this->addProperty(static::KEY_QUEUE_PROCESSOR_BATCH_SIZE, $batchSizeSchema);

        $expirationTimeSchema = new IntegerSchema(static::DEFAULT_QUEUE_EXPIRATION_TIME);
        $expirationTimeSchema->getRenderingDefinition()->setLabel('Expiration time (in days)');
        $this->addProperty(static::KEY_QUEUE_EXPIRATION_TIME, $expirationTimeSchema);

        $doneOnlySchema = new BooleanSchema(static::DEFAULT_QUEUE_CLEANUP_DONE_JOBS_ONLY);
        $doneOnlySchema->getRenderingDefinition()->setLabel('Cleanup only jobs with status "done"');
        $this->addProperty(static::KEY_QUEUE_CLEANUP_DONE_JOBS_ONLY, $doneOnlySchema);

        $maximumExecutionTimeSchema = new IntegerSchema(static::DEFAULT_QUEUE_MAXIMUM_EXECUTION_TIME);
        $maximumExecutionTimeSchema->getRenderingDefinition()->setLabel('Maximum execution time (in seconds)');
        $this->addProperty(static::KEY_QUEUE_MAXIMUM_EXECUTION_TIME, $maximumExecutionTimeSchema);

        $recogniseStuckJobsSchema = new BooleanSchema(static::DEFAULT_QUEUE_RECOGNISE_STUCK_JOBS);
        $recogniseStuckJobsSchema->getRenderingDefinition()->setLabel('Recognise stuck jobs and mark them as failed');
        $this->addProperty(static::KEY_QUEUE_RECOGNISE_STUCK_JOBS, $recogniseStuckJobsSchema);

        $rerunFailedJobsEnabledSchema = new BooleanSchema(static::DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_ENABLED);
        $rerunFailedJobsEnabledSchema->getRenderingDefinition()->setLabel('Enable re-running failed jobs');
        $this->addProperty(static::KEY_QUEUE_RE_RUN_FAILED_JOBS_ENABLED, $rerunFailedJobsEnabledSchema);

        $rerunFailedJobsAmountSchema = new IntegerSchema(static::DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT);
        $rerunFailedJobsAmountSchema->getRenderingDefinition()->setLabel('Number of times to re-run failed jobs');
        $this->addProperty(static::KEY_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT, $rerunFailedJobsAmountSchema);

        $rerunDelay = new IntegerSchema(static::DEFAULT_QUEUE_RE_RUN_DELAY);
        $rerunDelay->getRenderingDefinition()->setLabel('Delay before re-running failed jobs (in seconds)');
        $this->addProperty(static::KEY_QUEUE_RE_RUN_DELAY, $rerunDelay);
    }
}
