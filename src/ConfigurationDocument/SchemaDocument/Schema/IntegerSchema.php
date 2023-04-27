<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

class IntegerSchema extends ScalarValueSchema
{
    protected function getType(): string
    {
        return "INTEGER";
    }
}
