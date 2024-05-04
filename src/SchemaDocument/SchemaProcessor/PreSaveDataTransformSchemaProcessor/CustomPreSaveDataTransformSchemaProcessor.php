<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends PreSaveDataTransformSchemaProcessor<CustomSchema>
 */
class CustomPreSaveDataTransformSchemaProcessor extends PreSaveDataTransformSchemaProcessor
{
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void
    {
        $type = $schema->getType();
        $resolvedSchema = $this->schemaDocument->getCustomType($type);
        if (!$resolvedSchema instanceof SchemaInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('Custom schema "%s" not found in schema document!', $type));
        }

        $this->schemaProcessor->preSaveDataTransform($this->schemaDocument, $data, $resolvedSchema);
    }
}
