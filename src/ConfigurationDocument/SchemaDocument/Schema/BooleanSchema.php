<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class BooleanSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'BOOLEAN';
    }

    public function getDefaultValue(?SchemaDocument $schemaDocument = null): mixed
    {
        return parent::getDefaultValue($schemaDocument) ?? false;
    }
}
