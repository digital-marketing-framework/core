<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

trait SchemaProcessorAwareTrait
{
    protected SchemaProcessorInterface $schemaProcessor;

    public function setSchemaProcessor(SchemaProcessorInterface $schemaProcessor): void
    {
        $this->schemaProcessor = $schemaProcessor;
    }
}
