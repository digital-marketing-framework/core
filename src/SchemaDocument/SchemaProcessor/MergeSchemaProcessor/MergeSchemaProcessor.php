<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPlugin;

/**
 * @template SchemaType of SchemaInterface
 *
 * @implements MergeSchemaProcessorInterface<SchemaType>
 */
abstract class MergeSchemaProcessor extends SchemaProcessorPlugin implements MergeSchemaProcessorInterface
{
    abstract public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface;
}
