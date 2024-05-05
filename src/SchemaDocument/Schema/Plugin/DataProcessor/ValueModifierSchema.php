<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema\Plugin\DataProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

class ValueModifierSchema extends SwitchSchema
{
    public const TYPE = 'VALUE_MODIFIER';

    public function __construct(mixed $defaultValue = null)
    {
        parent::__construct('valueModifier', $defaultValue);
        $this->getRenderingDefinition()->setNavigationItem(false);
    }
}
