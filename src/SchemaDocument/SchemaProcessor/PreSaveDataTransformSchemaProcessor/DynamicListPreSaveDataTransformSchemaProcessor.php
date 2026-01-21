<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends PreSaveDataTransformSchemaProcessor<ListSchema|MapSchema>
 */
class DynamicListPreSaveDataTransformSchemaProcessor extends PreSaveDataTransformSchemaProcessor
{
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void
    {
        if ($data === null) {
            return;
        }

        if ($data === []) {
            $data = (object)[];
        } else {
            foreach (array_keys($data) as $key) {
                if (!isset($data[$key])) {
                    continue;
                }

                $this->schemaProcessor->preSaveDataTransform($this->schemaDocument, $data[$key], $schema->getItemSchema());
            }
        }
    }
}
