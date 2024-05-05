<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPluginInterface;

/**
 * @template SchemaType of SchemaInterface
 *
 * @extends SchemaProcessorPluginInterface<SchemaType>
 */
interface PreSaveDataTransformSchemaProcessorInterface extends SchemaProcessorPluginInterface
{
    /**
     * @param SchemaType $schema
     */
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void;
}
