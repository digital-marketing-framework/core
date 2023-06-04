<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;

class ValueSchema extends ContainerSchema
{
    public const TYPE = 'VALUE';

    public const KEY_VALUE_SOURCE = 'data';
    public const KEY_VALUE_MODIFIERS = 'modifiers';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $valueSource = new CustomSchema(ValueSourceSchema::TYPE);
        $valueSource->getRenderingDefinition()->setLabel('Value');
        $this->addProperty(static::KEY_VALUE_SOURCE, $valueSource);

        $valueModifiers = new CustomSchema(ValueModifierSchema::TYPE);
        $property = $this->addProperty('modifiers', $valueModifiers);
        $property->getRenderingDefinition()->setVisibilityConditionByValueSet('./data/type', 'valueSource/modifiable');
        $property->getRenderingDefinition()->setNavigationItem(false);
    }
}
