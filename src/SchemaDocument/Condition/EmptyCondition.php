<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Condition;

use BadMethodCallException;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class EmptyCondition extends Condition
{
    public function __construct(
        protected string $path = '.',
    ) {
        parent::__construct('empty');
    }

    /**
     * @return array{path:string}
     */
    protected function getConfig(): array
    {
        return [
            'path' => $this->path,
        ];
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     // TODO for this evaluation we need a configuration object
    //     throw new BadMethodCallException('EqualsCondition::evaluate() not implemented yet');
    // }
}
