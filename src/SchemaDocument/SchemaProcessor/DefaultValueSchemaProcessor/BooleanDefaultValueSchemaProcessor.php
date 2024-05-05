<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ScalarDefaultValueSchemaProcessor<BooleanSchema>
 */
class BooleanDefaultValueSchemaProcessor extends ScalarDefaultValueSchemaProcessor
{
    public function getDefaultValue(SchemaInterface $schema): bool
    {
        return (bool)(parent::getDefaultValue($schema) ?? false);
    }
}
