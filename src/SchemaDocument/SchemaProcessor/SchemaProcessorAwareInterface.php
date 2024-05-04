<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

interface SchemaProcessorAwareInterface
{
    public function setSchemaProcessor(SchemaProcessorInterface $schemaProcessor): void;
}
