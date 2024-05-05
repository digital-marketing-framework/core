<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends MergeSchemaProcessor<ListSchema>
 */
class ListMergeSchemaProcessor extends MergeSchemaProcessor
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        $valuesSchemaA = $schemaA->getValueSchema();
        $valuesSchemaB = $schemaB->getValueSchema();
        $valuesSchemaA = $this->schemaProcessor->merge($valuesSchemaA, $valuesSchemaB);
        $schemaA->setValueSchema($valuesSchemaA);

        return $schemaA;
    }
}
