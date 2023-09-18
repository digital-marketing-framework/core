<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use BadMethodCallException;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class UniqueCondition extends Condition
{
    public function __construct(
        protected string $valuePath,
        protected string $pathPattern
    ) {
        parent::__construct('unique');
    }

    /**
     * @return array{valuePath:string,pathPattern:string}
     */
    protected function getConfig(): array
    {
        return [
            'valuePath' => $this->valuePath,
            'pathPattern' => $this->pathPattern,
        ];
    }

    // public function evaluate(SchemaDocument $schemaDocument): bool
    // {
    //     // TODO for this evaluation we need a configuration object
    //     throw new BadMethodCallException('CountCondition::evaluate() not implemented yet');
    // }
}
