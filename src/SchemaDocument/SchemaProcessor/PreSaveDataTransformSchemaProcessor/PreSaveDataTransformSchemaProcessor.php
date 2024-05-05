<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor;

use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorPlugin;

/**
 * @template SchemaType of SchemaInterface
 *
 * @implements PreSaveDataTransformSchemaProcessorInterface<SchemaType>
 */
abstract class PreSaveDataTransformSchemaProcessor extends SchemaProcessorPlugin implements PreSaveDataTransformSchemaProcessorInterface
{
    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        protected SchemaDocument $schemaDocument,
    ) {
        parent::__construct($keyword, $registry);
    }

    abstract public function preSaveDataTransform(mixed &$data, SchemaInterface $schema): void;
}
