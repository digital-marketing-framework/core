<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ConvertValueTypesSchemaProcessor<IntegerSchema>
 */
class IntegerConvertValueTypesSchemaProcessor extends ConvertValueTypesSchemaProcessor
{
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void
    {
        $data = (int)$data;
    }
}
