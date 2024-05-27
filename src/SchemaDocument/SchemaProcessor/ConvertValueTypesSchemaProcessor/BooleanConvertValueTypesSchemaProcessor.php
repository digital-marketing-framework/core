<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ConvertValueTypesSchemaProcessor<BooleanSchema>
 */
class BooleanConvertValueTypesSchemaProcessor extends ConvertValueTypesSchemaProcessor
{
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void
    {
        $data = (bool)$data;
    }
}
