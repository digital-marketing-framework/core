<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends DefaultValueSchemaProcessor<ContainerSchema>
 */
class ContainerDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
{
    /**
     * @return array<string,mixed>
     */
    public function getDefaultValue(SchemaInterface $schema): array
    {
        $value = parent::getDefaultValue($schema);
        if ($value !== null) {
            if (!is_array($value)) {
                throw new DigitalMarketingFrameworkException('Default value for container type must be an array');
            }

            return $value;
        }

        $value = [];
        foreach ($schema->getProperties() as $property) {
            $value[$property->getName()] = $this->schemaProcessor->getDefaultValue($this->schemaDocument, $property->getSchema());
        }

        return $value;
    }
}
