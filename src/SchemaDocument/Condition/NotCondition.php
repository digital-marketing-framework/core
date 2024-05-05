<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class NotCondition extends Condition
{
    public function __construct(
        protected Condition $subCondition,
    ) {
        parent::__construct('not');
    }

    /**
     * @return array{type:string,config:array<mixed>}
     */
    protected function getConfig(): array
    {
        return $this->subCondition->toArray();
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     return !$this->subCondition->evaluate($schemaDocument);
    // }
}
