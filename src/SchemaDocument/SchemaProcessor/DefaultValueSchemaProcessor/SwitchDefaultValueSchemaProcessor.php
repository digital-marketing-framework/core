<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

/**
 * @extends DefaultValueSchemaProcessor<SwitchSchema>
 */
class SwitchDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
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

        $type = $this->schemaProcessor->getDefaultValue($this->schemaDocument, $schema->getTypeSchema());
        if ($type === '') {
            $value = [
                SwitchSchema::KEY_TYPE => '',
                SwitchSchema::KEY_CONFIG => [],
            ];
        } else {
            $config = $this->schemaProcessor->getDefaultValue($this->schemaDocument, $schema->getTypeSpecificConfigSchema($type));
            $value = [
                SwitchSchema::KEY_TYPE => $type,
                SwitchSchema::KEY_CONFIG => [
                    $type => $config,
                ],
            ];
        }

        foreach ($schema->getProperties() as $property) {
            $propertyName = $property->getName();
            if ($propertyName === SwitchSchema::KEY_TYPE || $propertyName === SwitchSchema::KEY_CONFIG) {
                continue;
            }

            $value[$propertyName] = $this->schemaProcessor->getDefaultValue($this->schemaDocument, $property->getSchema());
        }

        return $value;
    }
}
