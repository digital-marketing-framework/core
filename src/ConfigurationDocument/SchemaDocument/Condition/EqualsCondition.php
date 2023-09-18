<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class EqualsCondition extends Condition
{
    public function __construct(
        protected string $path,
        protected string $value,
    ) {
        parent::__construct('equals');
    }

    /**
     * @return array{path:string,value:string}
     */
    protected function getConfig(): array
    {
        return [
            'path' => $this->path,
            'value' => $this->value,
        ];
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     // TODO for this evaluation we need a configuration object
    //     throw new BadMethodCallException('EqualsCondition::evaluate() not implemented yet');
    // }
}
