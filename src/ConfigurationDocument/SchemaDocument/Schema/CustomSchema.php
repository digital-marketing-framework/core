<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class CustomSchema extends Schema
{
    public function __construct(
        protected string $type,
    ) {
        parent::__construct();
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
}
