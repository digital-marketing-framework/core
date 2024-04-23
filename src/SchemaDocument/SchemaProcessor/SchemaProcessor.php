<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;

class SchemaProcessor implements SchemaProcessorInterface
{
    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    protected function getSchemaKeyword(SchemaInterface $schema): string
    {
        $schemaType = $schema instanceof CustomSchema ? 'CUSTOM' : $schema->getType();

        return strtolower($schemaType);
    }

    public function merge(SchemaInterface $schemaA, SchemaInterface $schemaB): SchemaInterface
    {
        $keyword = $this->getSchemaKeyword($schemaA);
        $keywordB = $this->getSchemaKeyword($schemaB);

        if ($keyword !== $keywordB) {
            throw new DigitalMarketingFrameworkException(sprintf('Schemas of different types ("%s", "%s") cannot be merged', $keyword, $keywordB));
        }

        $processor = $this->registry->getMergeSchemaProcessor($keyword);
        if (!$processor instanceof MergeSchemaProcessorInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No merge schema processor found for keyword "%s"', $keyword));
        }

        return $processor->merge($schemaA, $schemaB);
    }

    public function getDefaultValue(SchemaDocument $schemaDocument, ?SchemaInterface $schema = null): mixed
    {
        if (!$schema instanceof SchemaInterface) {
            $schema = $schemaDocument->getMainSchema();
        }

        $keyword = $this->getSchemaKeyword($schema);
        $processor = $this->registry->getDefaultValueSchemaProcessor($keyword, $schemaDocument);

        if (!$processor instanceof DefaultValueSchemaProcessorInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No default value schema processor found for keyword "%s"', $keyword));
        }

        return $processor->getDefaultValue($schema);
    }
}
