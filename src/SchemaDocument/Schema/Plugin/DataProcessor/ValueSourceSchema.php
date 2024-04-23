<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class ValueSourceSchema extends SwitchSchema
{
    public const TYPE = 'VALUE_SOURCE';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct('valueSource', $defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
        $this->getRenderingDefinition()->setSkipHeader(true);
    }

    public function addModifiableKeyword(string $keyword): void
    {
        $this->addValueToValueSet($this->switchName . '/modifiable', $keyword);
    }

    public function addCanBeMultiValueKeyword(string $keyword): void
    {
        $this->addValueToValueSet($this->switchName . '/canBeMultiValue', $keyword);
    }
}
