<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

class BooleanSchema extends ScalarValueSchema
{
    protected function getType(): string
    {
        return "BOOLEAN";
    }
}
