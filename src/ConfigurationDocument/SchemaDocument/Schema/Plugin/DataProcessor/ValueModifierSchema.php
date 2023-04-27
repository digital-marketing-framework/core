<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\PluginSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;

class ValueModifierSchema extends PluginSchema
{
    public const TYPE = 'VALUE_MODIFIER';

    public const VALUE_SET_VALUE_MODIFIER = 'valueModifier/all';

    protected function init(): void
    {
        // $this->getRenderingDefinition()->setVisibilityConditionByValueSet('../data/type', ValueSourceSchema::VALUE_SET_VALUE_SOURCE_MODIFIABLE);
    }

    public function addValueModifier(string $keyword, SchemaInterface $schema): void
    {
        $property = $this->addProperty($keyword, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByToggle('./enabled');
        $this->valueSets[static::VALUE_SET_VALUE_MODIFIER][] = $keyword;
    }

    public function processPlugin(string $keyword, string $class): void
    {
        $this->addValueModifier($keyword, $class::getSchema());
    }

    protected function getPluginInterface(): string
    {
        return ValueModifierInterface::class;
    }
}
