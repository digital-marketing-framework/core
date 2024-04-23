<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ScalarDefaultValueSchemaProcessor<IntegerSchema>
 */
class IntegerDefaultValueSchemaProcessor extends ScalarDefaultValueSchemaProcessor
{
    public function getDefaultValue(SchemaInterface $schema): int
    {
        return (int)(parent::getDefaultValue($schema) ?? 0);
    }
}
