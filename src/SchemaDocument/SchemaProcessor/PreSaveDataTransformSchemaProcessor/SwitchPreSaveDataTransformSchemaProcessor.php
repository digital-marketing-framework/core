<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SwitchSchema;

/**
 * @extends PreSaveDataTransformSchemaProcessor<SwitchSchema>
 */
class SwitchPreSaveDataTransformSchemaProcessor extends PreSaveDataTransformSchemaProcessor
{
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void
    {
        if ($data === null) {
            return;
        }

        if (isset($data[SwitchSchema::KEY_TYPE])) {
            $this->schemaProcessor->preSaveDataTransform($this->schemaDocument, $data[SwitchSchema::KEY_TYPE], $schema->getTypeSchema());
        }

        if (($data[SwitchSchema::KEY_CONFIG] ?? []) === []) {
            $data[SwitchSchema::KEY_CONFIG] = (object)[];
        } else {
            foreach ($schema->getConfigSchema()->getProperties() as $property) {
                if (isset($data[SwitchSchema::KEY_CONFIG][$property->getName()])) {
                    $this->schemaProcessor->preSaveDataTransform($this->schemaDocument, $data[SwitchSchema::KEY_CONFIG][$property->getName()], $property->getSchema());
                }
            }
        }
    }
}
