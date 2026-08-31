<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;

/**
 * @template SchemaType of SchemaInterface
 *
 * @extends DefaultValueSchemaProcessor<SchemaType>
 */
abstract class ScalarDefaultValueSchemaProcessor extends DefaultValueSchemaProcessor
{
    // TODO take allowed values into account
    // public function getDefaultValue(SchemaInterface $schema): mixed
    // {
    //     return parent::getDefaultValue($schema) ?? $this->schemaProcessor->getFirstAllowedValue($this->schemaDocument, $schema);
    // }
}
