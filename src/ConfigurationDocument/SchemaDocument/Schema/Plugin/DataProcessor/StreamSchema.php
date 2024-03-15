<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class StreamSchema extends SwitchSchema
{
    public const TYPE = 'STREAM';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct('stream', $defaultValue);
    }
}
