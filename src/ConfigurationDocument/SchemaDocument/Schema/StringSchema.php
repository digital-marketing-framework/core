<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

class StringSchema extends ScalarValueSchema
{
    protected function getType(): string
    {
        return "STRING";
    }
}
