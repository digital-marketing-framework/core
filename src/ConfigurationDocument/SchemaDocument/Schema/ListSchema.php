<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\SchemaDocument;

class ListSchema extends Schema
{
    public function __construct(
        protected SchemaInterface $valueSchema = new ContainerSchema(),
    ) {
        parent::__construct();
    }

    protected function getType(): string
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

    protected function getConfig(): ?array
    {
        if (SchemaDocument::FLATTEN_SCHEMA) {
            return [
                'itemTemplate' => $this->valueSchema->toArray(),
            ];
        } else {
            return [
                'item' => $this->valueSchema->toArray(),
            ];
        }
    }
}
