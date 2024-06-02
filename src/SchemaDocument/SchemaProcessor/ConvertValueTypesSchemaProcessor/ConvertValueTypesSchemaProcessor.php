<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPlugin;

/**
 * @template SchemaType of SchemaInterface
 *
 * @implements ConvertValueTypesSchemaProcessorInterface<SchemaType>
 */
abstract class ConvertValueTypesSchemaProcessor extends SchemaProcessorPlugin implements ConvertValueTypesSchemaProcessorInterface
{
    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected SchemaDocument $schemaDocument,
    ) {
        parent::__construct($keyword, $registry);
    }

    abstract public function convertValueTypes(mixed &$data, SchemaInterface $schema): void;
}
