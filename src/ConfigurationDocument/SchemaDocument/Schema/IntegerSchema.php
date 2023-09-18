<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class IntegerSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'INTEGER';
    }

    public function getDefaultValue(?SchemaDocument $schemaDocument = null): mixed
    {
        return parent::getDefaultValue($schemaDocument) ?? 0;
    }
}
