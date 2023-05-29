<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class EvaluationSchema extends SwitchSchema
{
    public const TYPE = 'EVALUATION';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }

    protected function getSwitchName(): string
    {
        return 'evaluation';
    }
}
