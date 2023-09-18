<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

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

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $defaultValue = parent::getDefaultValue($schemaDocument);
        if ($defaultValue !== null) {
            return $defaultValue;
        }

        return $schemaDocument->getCustomType($this->getType())->getDefaultValue($schemaDocument);
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        $schemaDocument->getCustomType($this->getType())->preSaveDataTransform($value, $schemaDocument);
    }
}
