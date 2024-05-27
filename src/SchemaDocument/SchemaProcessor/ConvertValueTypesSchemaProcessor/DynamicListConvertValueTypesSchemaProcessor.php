<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ListSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\MapSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends ConvertValueTypesSchemaProcessor<ListSchema|MapSchema>
 */
class DynamicListConvertValueTypesSchemaProcessor extends ConvertValueTypesSchemaProcessor
{
    public function convertValueTypes(mixed &$data, SchemaInterface $schema): void
    {
        if ($data === null) {
            return;
        }

        if (!is_array($data)) {
            $data = [];
        } else {
            foreach (array_keys($data) as $key) {
                if (!isset($data[$key])) {
                    continue;
                }

                $this->schemaProcessor->convertValueTypes($this->schemaDocument, $data[$key], $schema->getItemSchema());
            }
        }
    }
}
