<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class AndCondition extends Condition
{
    /**
     * @param array<Condition> $subConditions
     */
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

    /**
     * @return array<array{type:string,config:array<mixed>}>
     */
    protected function getConfig(): array
    {
        return array_map(static fn (Condition $condition) => $condition->toArray(), $this->subConditions);
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
