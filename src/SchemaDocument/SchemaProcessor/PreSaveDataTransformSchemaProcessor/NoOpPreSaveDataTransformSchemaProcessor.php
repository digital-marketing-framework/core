<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends PreSaveDataTransformSchemaProcessor<SchemaInterface>
 */
class NoOpPreSaveDataTransformSchemaProcessor extends PreSaveDataTransformSchemaProcessor
{
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void
    {
    }
}
