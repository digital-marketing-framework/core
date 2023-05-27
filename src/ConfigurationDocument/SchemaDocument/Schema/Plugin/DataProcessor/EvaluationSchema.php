<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class EvaluationSchema extends SwitchSchema
{
    public const TYPE = 'EVALUATION';

    protected function getSwitchName(): string
    {
        return 'evaluation';
    }
}
