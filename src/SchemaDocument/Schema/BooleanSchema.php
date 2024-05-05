<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

class BooleanSchema extends ScalarValueSchema
{
    public function getType(): string
    {
        return 'BOOLEAN';
    }
}
