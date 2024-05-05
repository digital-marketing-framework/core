<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;

/**
 * @extends MergeSchemaProcessor<BooleanSchema|IntegerSchema|StringSchema>
 */
class ScalarMergeSchemaProcessor extends MergeSchemaProcessor
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        return $schemaB;
    }
}
