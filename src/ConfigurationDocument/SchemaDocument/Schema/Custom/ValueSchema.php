<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Custom;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueModifierSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\ValueSourceSchema;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;

class ValueSchema extends ContainerSchema
{
    public const TYPE = 'VALUE';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct($defaultValue);

        $valueSource = new CustomSchema(ValueSourceSchema::TYPE);
        $valueSource->getRenderingDefinition()->setLabel('Value');
        $this->addProperty(DataProcessor::KEY_DATA, $valueSource);

        $valueModifier = new CustomSchema(ValueModifierSchema::TYPE);
        $valueModifiers = new ListSchema($valueModifier);
        $this->addProperty(DataProcessor::KEY_MODIFIERS, $valueModifiers);
    }
}
