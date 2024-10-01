<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

class CustomSchema extends Schema
{
    public function __construct(
        protected string $type,
        mixed $defaultValue = null,
    ) {
        parent::__construct($defaultValue);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
