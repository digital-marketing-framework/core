<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;

class ValueSchema extends ContainerSchema
{
    public const TYPE = 'VALUE';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);
        $this->addProperty('data', new CustomSchema(ValueSourceSchema::TYPE));
        $property = $this->addProperty('modifiers', new CustomSchema(ValueModifierSchema::TYPE));
        $property->getRenderingDefinition()->setVisibilityConditionByValueSet('./data/type', 'valueSource/modifiable');
        $property->getRenderingDefinition()->setNavigationItem(false);
    }
}
