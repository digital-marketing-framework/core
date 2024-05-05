<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends MergeSchemaProcessor<CustomSchema>
 */
class CustomMergeSchemaProcessor extends MergeSchemaProcessor
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        return $schemaB;
    }
}
