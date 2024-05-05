<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends DefaultValueSchemaProcessor<CustomSchema>
 */
class CustomDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
{
    public function getDefaultValue(SchemaInterface $schema): mixed
    {
        $value = parent::getDefaultValue($schema);
        if ($value !== null) {
            return $value;
        }

        $customType = $schema->getType();
        $customSchema = $this->schemaDocument->getCustomType($customType);

        if (!$customSchema instanceof SchemaInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('Custom schema type "%s" not found', $customType));
        }

        return $this->schemaProcessor->getDefaultValue($this->schemaDocument, $customSchema);
    }
}
