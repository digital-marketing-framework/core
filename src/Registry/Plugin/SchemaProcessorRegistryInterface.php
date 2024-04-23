<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;

interface SchemaProcessorRegistryInterface extends PluginRegistryInterface
{
    public function getSchemaProcessor(): SchemaProcessorInterface;

    public function setSchemaProcessor(SchemaProcessorInterface $schemaProcessor): void;

    // -- MergeSchemaProcessor --

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerMergeSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return ?MergeSchemaProcessorInterface<SchemaInterface>
     */
    public function getMergeSchemaProcessor(string $keyword): ?MergeSchemaProcessorInterface;

    public function deleteMergeSchemaProcessor(string $keyword): void;

    // -- AllowedValuesSchemaProcessor --
    // -- DefaultValueSchemaProcessor --

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerDefaultValueSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return DefaultValueSchemaProcessorInterface<SchemaInterface>
     */
    public function getDefaultValueSchemaProcessor(string $keyword, SchemaDocument $schemaDocument): ?DefaultValueSchemaProcessorInterface;

    public function deleteDefaultValueSchemaProcessor(string $keyword): void;

    // -- PreSaveDataSchemaProcessor --
    // -- SuggestedValuesSchemaProcessor --
    // -- ValidationSchemaProcessor --
}
