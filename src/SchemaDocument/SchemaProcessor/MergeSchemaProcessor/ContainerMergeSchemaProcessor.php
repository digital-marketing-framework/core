<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends MergeSchemaProcessor<ContainerSchema>
 */
class ContainerMergeSchemaProcessor extends MergeSchemaProcessor
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        foreach ($schemaB->getProperties() as $propertyName => $propertyB) {
            $childSchemaB = $propertyB->getSchema();
            $childSchemaA = $schemaA->getProperty($propertyName)?->getSchema();
            if ($childSchemaA instanceof SchemaInterface) {
                $childSchemaA = $this->schemaProcessor->merge($childSchemaA, $childSchemaB);
                $schemaA->addProperty($propertyName, $childSchemaA, overwrite: true);
            } else {
                $propertyA = $schemaA->addProperty($propertyName, $childSchemaB);
                $propertyA->setRenderingDefinition($propertyB->getRenderingDefinition());
                $propertyA->setWeight($propertyB->getWeight());
            }
        }

        return $schemaA;
    }
}
