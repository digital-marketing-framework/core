<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class MapSchema extends ListSchema
{
    public function __construct(
        SchemaInterface $valueSchema = new ContainerSchema(),
        protected StringSchema $nameSchema = new StringSchema('mapKey'),
        mixed $defaultValue = null,
    ) {
        parent::__construct($valueSchema, $defaultValue);
    }

    public function getType(): string
    {
        return "MAP";
    }

    public function getNameSchema(): StringSchema
    {
        return $this->nameSchema;
    }

    public function setNameSchema(StringSchema $nameSchema): void
    {
        $this->nameSchema = $nameSchema;
    }

    public function getValueSets(): array
    {
        return $this->mergeValueSets(parent::getValueSets(), $this->nameSchema->getValueSets());
    }

    protected function getConfig(): array
    {
        if (SchemaDocument::FLATTEN_SCHEMA) {
            return [
                'keyTemplate' => $this->nameSchema->toArray(),
            ] + parent::getConfig();
        } else {
            return [
                'key' => $this->nameSchema->toArray(),
            ] + parent::getConfig();
        }
    }

    public function getDefaultValue(SchemaDocument $schemaDocument): mixed
    {
        $defaultValue = parent::getDefaultValue($schemaDocument);
        if (is_array($defaultValue) && empty($defaultValue)) {
            $defaultValue = [];
        }
        return $defaultValue;
    }

    public function preSaveDataTransform(mixed &$value, SchemaDocument $schemaDocument): void
    {
        if (empty($value)) {
            $value = (object)[];
        } else {
            foreach (array_keys($value) as $key) {
                $this->valueSchema->preSaveDataTransform($value[$key], $schemaDocument);
            }
        }
    }
}
