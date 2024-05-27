<?php

namespace DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaDocument;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\ConvertValueTypesSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\PreSaveDataTransformSchemaProcessorInterface;

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

    /**
     * This method is oddly named, which is because its purpose is odd too.
     * Unfortunately, some configuration document producers need to adjust
     * the PHP data according to its schema before they can perform the document production.
     *
     * For example, the empty PHP array [] can be interpreted as an empty list and an empty
     * associative object, which is expressed differently in both YAML and JSON: "{}" vs "[]"
     * That is why we need to read the schema to be able to tell, which empty array is supposed
     * to become what kind of value in the produced document.
     */
    public function preSaveDataTransform(SchemaDocument $schemaDocument, mixed &$data, ?SchemaInterface $schema = null): void
    {
        if (!$schema instanceof SchemaInterface) {
            $schema = $schemaDocument->getMainSchema();
        }

        $keyword = $this->getSchemaKeyword($schema);
        $processor = $this->registry->getPreSaveDataTransformSchemaProcessor($keyword, $schemaDocument);

        if (!$processor instanceof PreSaveDataTransformSchemaProcessorInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No pre-save-data-transform processor found for keyword "%s"', $keyword));
        }

        $processor->preSaveDataTransform($data, $schema);
    }

    /**
     * Use this service to convert the types of values where needed.
     *
     * Some systems don't save values type-safe. For example, a boolean true could be stored as integer 1 or even string '1'.
     * This processor will cast those value types so that they are matching their intended type again.
     */
    public function convertValueTypes(SchemaDocument $schemaDocument, mixed &$data, ?SchemaInterface $schema = null): void
    {
        if (!$schema instanceof SchemaInterface) {
            $schema = $schemaDocument->getMainSchema();
        }

        $keyword = $this->getSchemaKeyword($schema);
        $processor = $this->registry->getConvertValuesSchemaProcessor($keyword, $schemaDocument);

        if (!$processor instanceof ConvertValueTypesSchemaProcessorInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('No convert-values processor found for keyword "%s"', $keyword));
        }

        $processor->convertValueTypes($data, $schema);
    }
}
