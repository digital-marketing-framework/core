<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerProperty;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @extends PreSaveDataTransformSchemaProcessor<ContainerSchema>
 */
class ContainerPreSaveDataTransformSchemaProcessor extends PreSaveDataTransformSchemaProcessor
{
    public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void
    {
        if ($data === null) {
            return;
        }

        if (!is_array($data) || $data === []) {
            $data = (object)[];
        } else {
            foreach (array_keys($data) as $key) {
                $property = $schema->getProperty($key);
                if ($property instanceof ContainerProperty) {
                    // TODO unknown data should be allowed due to previous schema versions
                    //      however, if we can run migrations first, this would be different
                    //      eventually we do want to cleanup and/or validate a configuration completely
                    //      but probably not in this method

                    $this->schemaProcessor->preSaveDataTransform($this->schemaDocument, $data[$key], $property->getSchema());
                }
            }
        }
    }
}
