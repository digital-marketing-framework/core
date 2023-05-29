<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;

class ValueModifierSchema extends ContainerSchema
{
    public const TYPE = 'VALUE_MODIFIER';

    public const VALUE_SET_VALUE_MODIFIER = 'valueModifier/all';
    public const VALUE_SET_VALUE_MODIFIER_MODIFIABLE = 'valueModifier/modifiable';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->getRenderingDefinition()->setVisibilityConditionByValueSet('../data/type', static::VALUE_SET_VALUE_MODIFIER_MODIFIABLE);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }

    public function addItem(string $keyword, SchemaInterface $schema): void
    {
        $property = $this->addProperty($keyword, $schema);
        $property->getRenderingDefinition()->setVisibilityConditionByToggle('./enabled');
        $this->addValueToValueSet(static::VALUE_SET_VALUE_MODIFIER, $keyword);
    }
}
