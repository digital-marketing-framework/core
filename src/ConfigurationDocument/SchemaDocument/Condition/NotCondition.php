<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class NotCondition extends Condition
{
    public function __construct(
        protected Condition $subCondition,
    ) {
        parent::__construct('not');
    }

    protected function getConfig(): array
    {
        return $this->subCondition->toArray();
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     return !$this->subCondition->evaluate($schemaDocument);
    // }
}
