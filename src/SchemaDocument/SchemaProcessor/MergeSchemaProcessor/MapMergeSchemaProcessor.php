<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

/**
 * @extends MergeSchemaProcessor<MapSchema>
 */
class MapMergeSchemaProcessor extends MergeSchemaProcessor
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        $nameSchemaA = $schemaA->getNameSchema();
        $nameSchemaB = $schemaB->getNameSchema();
        /** @var StringSchema */
        $nameSchema = $this->schemaProcessor->merge($nameSchemaA, $nameSchemaB);
        $schemaA->setNameSchema($nameSchema);

        $valueSchemaA = $schemaA->getValueSchema();
        $valueSchemaB = $schemaB->getValueSchema();
        $valueSchema = $this->schemaProcessor->merge($valueSchemaA, $valueSchemaB);
        $schemaA->setValueSchema($valueSchema);

        return $schemaA;
    }
}
