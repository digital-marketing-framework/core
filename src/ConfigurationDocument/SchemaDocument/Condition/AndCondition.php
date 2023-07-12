<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class AndCondition extends Condition
{
    public function __construct(
        protected array $subConditions = [],
    ) {
        parent::__construct('and');
    }

    public function getConditionCount(): int
    {
        return count($this->subConditions);
    }

    public function addCondition(Condition $condition): void
    {
        $this->subConditions[] = $condition;
    }

    protected function getConfig(): array
    {
        return array_map(function(Condition $condition) {
            return $condition->toArray();
        }, $this->subConditions);
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     foreach ($this->subConditions as $condition) {
    //         if (!$condition->evaluate($schemaDocument)) {
    //             return false;
    //         }
    //     }
    //     return true;
    // }
}
