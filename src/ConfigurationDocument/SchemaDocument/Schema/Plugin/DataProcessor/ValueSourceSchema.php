<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;

class ValueSourceSchema extends SwitchSchema
{
    public const TYPE = 'VALUE_SOURCE';

    protected function getSwitchName(): string
    {
        return 'valueSource';
    }

    public function addModifiableKeyword(string $keyword): void
    {
        $this->valueSets[$this->getSwitchName() . '/modifiable'][] = $keyword;
    }

    public function addCanBeMultiValueKeyword(string $keyword): void
    {
        $this->valueSets[$this->getSwitchName() . '/canBeMultiValue'][] = $keyword;
    }
}
