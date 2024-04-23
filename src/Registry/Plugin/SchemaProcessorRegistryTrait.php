<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\SchemaProcessorInterface;

trait SchemaProcessorRegistryTrait
{
    use PluginRegistryTrait;

    abstract public function createObject(string $class, array $arguments = []): object;

    protected SchemaProcessorInterface $schemaProcessor;

    public function getSchemaProcessor(): SchemaProcessorInterface
    {
        if (!isset($this->schemaProcessor)) {
            $this->schemaProcessor = $this->createObject(SchemaProcessor::class, [$this]);
        }

        return $this->schemaProcessor;
    }

    public function setSchemaProcessor(SchemaProcessorInterface $schemaProcessor): void
    {
        $this->schemaProcessor = $schemaProcessor;
    }

    // -- MergeSchemaProcessor --

    public function registerMergeSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(MergeSchemaProcessorInterface::class, $class, $additionalArguments, $keyword);
    }

    public function getMergeSchemaProcessor(string $keyword): ?MergeSchemaProcessorInterface
    {
        return $this->getPlugin($keyword, MergeSchemaProcessorInterface::class);
    }

    public function deleteMergeSchemaProcessor(string $keyword): void
    {
        $this->deletePlugin($keyword, MergeSchemaProcessorInterface::class);
    }

    // -- AllowedValuesSchemaProcessor --
    // -- DefaultValueSchemaProcessor --

    public function registerDefaultValueSchemaProcessor(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(DefaultValueSchemaProcessorInterface::class, $class, $additionalArguments, $keyword);
    }

    public function getDefaultValueSchemaProcessor(string $keyword, SchemaDocument $schemaDocument): ?DefaultValueSchemaProcessorInterface
    {
        return $this->getPlugin($keyword, DefaultValueSchemaProcessorInterface::class, [$schemaDocument]);
    }

    public function deleteDefaultValueSchemaProcessor(string $keyword): void
    {
        $this->deletePlugin($keyword, DefaultValueSchemaProcessorInterface::class);
    }

    // -- PreSaveDataSchemaProcessor --
    // -- SuggestedValuesSchemaProcessor --
    // -- ValidationSchemaProcessor --
}
