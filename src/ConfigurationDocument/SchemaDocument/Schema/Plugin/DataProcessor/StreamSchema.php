<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom\FieldContextSelectionSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class StreamSchema extends SwitchSchema
{
    public const TYPE = 'STREAM';

    public function __construct(mixed $defaultValue = null)
    {
        $this->addProperty('inputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_INPUT))->setWeight(-2);
        $this->addProperty('outputContext', new CustomSchema(FieldContextSelectionSchema::TYPE_OUTPUT))->setWeight(-1);
        parent::__construct('stream', $defaultValue);
    }
}
