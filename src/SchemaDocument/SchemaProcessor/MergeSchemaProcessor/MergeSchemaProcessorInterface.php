<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPluginInterface;

/**
 * @template SchemaType of SchemaInterface
 *
 * @extends SchemaProcessorPluginInterface<SchemaType>
 */
interface MergeSchemaProcessorInterface extends SchemaProcessorPluginInterface
{
    /**
     * @param SchemaType $schemaA
     * @param SchemaType $schemaB
     *
     * @return SchemaType
     */
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface;
}
