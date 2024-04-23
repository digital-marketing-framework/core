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
    public function getDefaultValue(SchemaInterface $schema): mixed
    {
        // TODO take allowed values into account
        // return parent::getDefaultValue($schema) ?? $this->schemaProcessor->getFirstAllowedValue($this->schemaDocument, $schema);

        return parent::getDefaultValue($schema);
    }
}
