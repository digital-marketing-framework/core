<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\ConvertValueTypesSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\PreSaveDataTransformSchemaProcessorInterface;
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

    // -- DefaultValueSchemaProcessor --

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerDefaultValueSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return ?DefaultValueSchemaProcessorInterface<SchemaInterface>
     */
    public function getDefaultValueSchemaProcessor(string $keyword, SchemaDocument $schemaDocument): ?DefaultValueSchemaProcessorInterface;

    public function deleteDefaultValueSchemaProcessor(string $keyword): void;

    // -- PreSaveDataSchemaProcessor --

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerPreSaveDataTransformSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return ?PreSaveDataTransformSchemaProcessorInterface<SchemaInterface>
     */
    public function getPreSaveDataTransformSchemaProcessor(string $keyword, SchemaDocument $schemaDocument): ?PreSaveDataTransformSchemaProcessorInterface;

    public function deletePreSaveDataTransformSchemaProcessor(string $keyword): void;

    // -- ConvertValueTypesSchemaProcessor --

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerConvertValuesSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void;

    /**
     * @return ?ConvertValueTypesSchemaProcessorInterface<SchemaInterface>
     */
    public function getConvertValuesSchemaProcessor(string $keyword, SchemaDocument $schemaDocument): ?ConvertValueTypesSchemaProcessorInterface;

    public function deleteConvertValuesSchemaProcessor(string $keyword): void;

    // -- TODO AllowedValuesSchemaProcessor --
    // -- TODO SuggestedValuesSchemaProcessor --
    // -- TODO ValidationSchemaProcessor --
}
