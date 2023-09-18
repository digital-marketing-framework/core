<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Condition;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

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
