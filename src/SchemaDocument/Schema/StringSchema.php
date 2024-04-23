<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

class StringSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'STRING';
    }
}
