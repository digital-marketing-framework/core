<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ConvertValueTypesSchemaProcessor<CustomSchema>
 */
class CustomConvertValueTypesSchemaProcessor extends ConvertValueTypesSchemaProcessor
{
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void
    {
        $type = $schema->getType();
        $resolvedSchema = $this->schemaDocument->getCustomType($type);
        if (!$resolvedSchema instanceof SchemaInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('Custom schema "%s" not found in schema document!', $type));
        }

        $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data, $resolvedSchema);
    }
}
