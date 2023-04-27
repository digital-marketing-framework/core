<?php

namespace DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema;

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
        return [
            'itemSchema' => $this->valueSchema->toArray(),
        ];
    }
}
