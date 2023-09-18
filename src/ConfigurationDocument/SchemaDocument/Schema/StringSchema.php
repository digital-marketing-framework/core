<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class StringSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'STRING';
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        return parent::getDefaultValue($schemaDocument) ?? '';
    }
}
