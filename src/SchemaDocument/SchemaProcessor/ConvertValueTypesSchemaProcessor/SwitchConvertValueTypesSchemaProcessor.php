<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

/**
 * @extends ConvertValueTypesSchemaProcessor<SwitchSchema>
 */
class SwitchConvertValueTypesSchemaProcessor extends ConvertValueTypesSchemaProcessor
{
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void
    {
        if ($data === null) {
            return;
        }

        if (isset($data[SwitchSchema::KEY_TYPE])) {
            $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data[SwitchSchema::KEY_TYPE], $schema->getTypeSchema());
        }

        foreach ($schema->getConfigSchema()->getProperties() as $property) {
            if (isset($data[SwitchSchema::KEY_CONFIG][$property->getName()])) {
                $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data[SwitchSchema::KEY_CONFIG][$property->getName()], $property->getSchema());
            }
        }
    }
}
