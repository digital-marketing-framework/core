<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;

interface SchemaProcessorInterface
{
    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface;

    public function getDefaultValue(SchemaDocument $schemaDocument, ?SchemaInterface $schema = null): mixed;

    public function preSaveDataTransform(SchemaDocument $schemaDocument, mixed &$data, ?SchemaInterface $schema = null): void;

    public function convertValueTypes(SchemaDocument $schemaDocument, mixed &$data, ?SchemaInterface $schema = null): void;
}
