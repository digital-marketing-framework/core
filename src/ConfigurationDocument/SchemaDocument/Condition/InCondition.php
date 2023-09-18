<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Value\ScalarValues;

class InCondition extends Condition
{
    public function __construct(
        protected string $path,
        protected ScalarValues $values = new ScalarValues(),
    ) {
        parent::__construct('in');
    }

    public function getValues(): ScalarValues
    {
        return $this->values;
    }

    /**
     * @return array{path:string,list:array<string,mixed>}
     */
    protected function getConfig(): array
    {
        return [
            'path' => $this->path,
            'list' => $this->values->toArray(),
        ];
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     // TODO for this evaluation we need a configuration object
    //     throw new BadMethodCallException('InCondition::evaluate() not implemented yet');
    // }
}
