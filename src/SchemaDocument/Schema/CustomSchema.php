<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

class CustomSchema extends Schema
{
    public function __construct(
        protected string $type,
        mixed $defaultValue = null
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

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        $schemaDocument->getCustomType($this->getType())->preSaveDataTransform($value, $schemaDocument);
    }
}
