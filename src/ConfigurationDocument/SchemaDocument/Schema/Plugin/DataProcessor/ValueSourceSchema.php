<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class ValueSourceSchema extends SwitchSchema
{
    public const TYPE = 'VALUE_SOURCE';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }

    protected function getSwitchName(): string
    {
        return 'valueSource';
    }

    public function addModifiableKeyword(string $keyword): void
    {
        $this->addValueToValueSet($this->getSwitchName() . '/modifiable', $keyword);
    }

    public function addCanBeMultiValueKeyword(string $keyword): void
    {
        $this->addValueToValueSet($this->getSwitchName() . '/canBeMultiValue', $keyword);
    }
}
