<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPluginInterface;

/**
 * @template SchemaType of SchemaInterface
 *
 * @extends SchemaProcessorPluginInterface<SchemaType>
 */
interface ConvertValueTypesSchemaProcessorInterface extends SchemaProcessorPluginInterface
{
    /**
     * @param SchemaType $schema
     */
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void;
}
