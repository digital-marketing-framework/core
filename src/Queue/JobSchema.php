<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinition;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

class JobSchema extends ContainerSchema
{
    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $this->addProperty('environment', new StringSchema(''));

        // TODO render format should be a date/time string
        $this->addProperty('created', new IntegerSchema(0));
        $this->addProperty('changed', new IntegerSchema(0));

        $this->addProperty('status', new IntegerSchema(QueueInterface::STATUS_QUEUED));
        $this->addProperty('skipped', new BooleanSchema(false));
        $this->addProperty('status_message', new StringSchema(''));
        $this->addProperty('retry_amount', new IntegerSchema(0));

        $this->addProperty('hash', new StringSchema(''));
        $this->addProperty('label', new StringSchema(''));
        $this->addProperty('type', new StringSchema(''));

        $serializedDataSchema = new StringSchema('');
        $serializedDataSchema->getRenderingDefinition()->setFormat(RenderingDefinition::FORMAT_TEXT);
        $this->addProperty('serialized_data', $serializedDataSchema);
    }
}
