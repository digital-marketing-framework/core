<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

class IntegerSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'INTEGER';
    }
}
