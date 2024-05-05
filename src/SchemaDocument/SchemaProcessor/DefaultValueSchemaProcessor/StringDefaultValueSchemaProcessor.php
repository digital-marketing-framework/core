<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

/**
 * @extends ScalarDefaultValueSchemaProcessor<StringSchema>
 */
class StringDefaultValueSchemaProcessor extends ScalarDefaultValueSchemaProcessor
{
    public function getDefaultValue(SchemaInterface $schema): string
    {
        return parent::getDefaultValue($schema) ?? '';
    }
}
