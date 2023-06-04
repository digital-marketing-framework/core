<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class ListSchema extends Schema
{
    public function __construct(
        protected SchemaInterface $valueSchema = new ContainerSchema(),
        mixed $defaultValue = null,
    ) {
        parent::__construct($defaultValue);
    }

    public function getType(): string
    {
        return "LIST";
    }

    public function getValueSchema(): SchemaInterface
    {
        return $this->valueSchema;
    }

    public function setValueSchema(SchemaInterface $valueSchema): void
    {
        $this->valueSchema = $valueSchema;
    }

    public function getValueSets(): array
    {
        return $this->mergeValueSets(parent::getValueSets(), $this->valueSchema->getValueSets());
    }

    protected function getConfig(): array
    {
        if (SchemaDocument::FLATTEN_SCHEMA) {
            return parent::getConfig() + ['valueTemplate' => $this->valueSchema->toArray()];
        } else {
            return parent::getConfig() + ['value' => $this->valueSchema->toArray()];
        }
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        return parent::getDefaultValue($schemaDocument) ?? [];
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        foreach (array_keys($value) as $key) {
            $this->valueSchema->preSaveDataTransform($value[$key], $schemaDocument);
        }
    }
}
