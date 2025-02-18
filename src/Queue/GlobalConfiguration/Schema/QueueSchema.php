<?php

namespace DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;

class QueueSchema extends ContainerSchema
{
    public const KEY_QUEUE = 'queue';

    public const KEY_QUEUE_MAXIMUM_EXECUTION_TIME = 'maxExecutionTime';

    public const DEFAULT_QUEUE_MAXIMUM_EXECUTION_TIME = 600;

    public const KEY_QUEUE_EXPIRATION_TIME = 'expirationTime';

    public const DEFAULT_QUEUE_EXPIRATION_TIME = 30;

    public const KEY_QUEUE_RE_RUN_FAILED_JOBS_ENABLED = 'rerunEnabled';

    public const DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_ENABLED = false;

    public const KEY_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT = 'rerunFailedJobsAmount';

    public const DEFAULT_QUEUE_RE_RUN_FAILED_JOBS_AMOUNT = 3;

    public const KEY_QUEUE_RE_RUN_DELAY = 'rerunDelay';

    public const DEFAULT_QUEUE_RE_RUN_DELAY = 300;

    public function __construct()
    {
        parent::__construct();

        $maximumExecutionTimeSchema = new IntegerSchema(static::DEFAULT_QUEUE_MAXIMUM_EXECUTION_TIME);
        $maximumExecutionTimeSchema->getRenderingDefinition()->setLabel('Maximum execution time (in seconds)');
        $this->addProperty(static::KEY_QUEUE_MAXIMUM_EXECUTION_TIME, $maximumExecutionTimeSchema);

        $expirationTimeSchema = new IntegerSchema(static::DEFAULT_QUEUE_EXPIRATION_TIME);
        $expirationTimeSchema->getRenderingDefinition()->setLabel('Expiration time (in days)');
        $this->addProperty(static::KEY_QUEUE_EXPIRATION_TIME, $expirationTimeSchema);

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
