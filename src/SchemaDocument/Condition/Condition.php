<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

abstract class Condition
{
    public function __construct(
        protected string $type,
    ) {
    }

    /**
     * @return array<mixed>
     */
    abstract protected function getConfig(): array;

    /**
     * @return array{type:string,config:array<mixed>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'config' => $this->getConfig(),
        ];
    }

    // abstract public function evaluate(SchemaDocument $schemaDocument): bool;
}
