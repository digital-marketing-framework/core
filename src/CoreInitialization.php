<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ExistsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\InComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsEmptyComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsFalseComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsTrueComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\RegExpComparison;
use DigitalMarketingFramework\Core\DataProcessor\Condition\AndCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ComparisonCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ConditionInterface;
use DigitalMarketingFramework\Core\DataProcessor\Condition\FalseCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\NotCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\OrCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ReferenceCondition;
use DigitalMarketingFramework\Core\DataProcessor\Condition\TrueCondition;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\ExcludeFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\IgnoreEmptyFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PrefixDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\DataMapperGroupInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SequenceDataMapperGroup;
use DigitalMarketingFramework\Core\DataProcessor\DataMapperGroup\SingleDataMapperGroup;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\DefaultValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\IndexValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\InsertDataValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\JoinValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\LowerCaseValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapReferenceValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\MapValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\NegateValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SpliceValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\SprintfValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\TrimValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\UpperCaseValueModifier;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\BooleanValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConcatenationValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FileValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FirstOfValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\IntegerValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ListValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\MultiValueValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\SwitchValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\SchemaDocument\RenderingDefinition\RenderingDefinitionInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\BooleanSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\IntegerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\StringSchema;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\BooleanConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\ContainerConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\ConvertValueTypesSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\CustomConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\DynamicListConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\IntegerConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\StringConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\ConvertValueTypesSchemaProcessor\SwitchConvertValueTypesSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\BooleanDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\ContainerDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\CustomDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\DefaultValueSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\IntegerDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\ListDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\MapDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\StringDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\DefaultValueSchemaProcessor\SwitchDefaultValueSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\ContainerMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\CustomMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\ListMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MapMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\MergeSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\ScalarMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\MergeSchemaProcessor\SwitchMergeSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\ContainerPreSaveDataTransformSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\CustomPreSaveDataTransformSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\DynamicListPreSaveDataTransformSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\NoOpPreSaveDataTransformSchemaProcessor;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\PreSaveDataTransformSchemaProcessorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\SchemaProcessor\PreSaveDataTransformSchemaProcessor\SwitchPreSaveDataTransformSchemaProcessor;

class CoreInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::CORE => [
            ValueSourceInterface::class => [
                BooleanValueSource::class,
                ConcatenationValueSource::class,
                ConditionValueSource::class,
                ConstantValueSource::class,
                FieldCollectorValueSource::class,
                FieldValueSource::class,
                FileValueSource::class,
                FirstOfValueSource::class,
                IntegerValueSource::class,
                ListValueSource::class,
                MultiValueValueSource::class,
                NullValueSource::class,
                SwitchValueSource::class,
            ],
            ValueModifierInterface::class => [
                DefaultValueModifier::class,
                IndexValueModifier::class,
                InsertDataValueModifier::class,
                JoinValueModifier::class,
                LowerCaseValueModifier::class,
                MapReferenceValueModifier::class,
                MapValueModifier::class,
                NegateValueModifier::class,
                SpliceValueModifier::class,
                SprintfValueModifier::class,
                TrimValueModifier::class,
                UpperCaseValueModifier::class,
            ],
            ConditionInterface::class => [
                AndCondition::class,
                ComparisonCondition::class,
                FalseCondition::class,
                NotCondition::class,
                OrCondition::class,
                ReferenceCondition::class,
                TrueCondition::class,
            ],
            ComparisonInterface::class => [
                EqualsComparison::class,
                ExistsComparison::class,
                InComparison::class,
                IsEmptyComparison::class,
                IsFalseComparison::class,
                IsTrueComparison::class,
                RegExpComparison::class,
            ],
            DataMapperInterface::class => [
                PrefixDataMapper::class,
                ExcludeFieldsDataMapper::class,
                FieldMapDataMapper::class,
                IgnoreEmptyFieldsDataMapper::class,
                PassthroughFieldsDataMapper::class,
            ],
            DataMapperGroupInterface::class => [
                SingleDataMapperGroup::class,
                SequenceDataMapperGroup::class,
            ],
            MergeSchemaProcessorInterface::class => [
                'boolean' => ScalarMergeSchemaProcessor::class,
                'container' => ContainerMergeSchemaProcessor::class,
                'custom' => CustomMergeSchemaProcessor::class,
                'integer' => ScalarMergeSchemaProcessor::class,
                'list' => ListMergeSchemaProcessor::class,
                'map' => MapMergeSchemaProcessor::class,
                'string' => ScalarMergeSchemaProcessor::class,
                'switch' => SwitchMergeSchemaProcessor::class,
            ],
            DefaultValueSchemaProcessorInterface::class => [
                'boolean' => BooleanDefaultValueSchemaProcessor::class,
                'container' => ContainerDefaultValueSchemaProcessor::class,
                'custom' => CustomDefaultValueSchemaProcessor::class,
                'integer' => IntegerDefaultValueSchemaProcessor::class,
                'list' => ListDefaultValueSchemaProcessor::class,
                'map' => MapDefaultValueSchemaProcessor::class,
                'string' => StringDefaultValueSchemaProcessor::class,
                'switch' => SwitchDefaultValueSchemaProcessor::class,
            ],
            PreSaveDataTransformSchemaProcessorInterface::class => [
                'boolean' => NoOpPreSaveDataTransformSchemaProcessor::class,
                'container' => ContainerPreSaveDataTransformSchemaProcessor::class,
                'custom' => CustomPreSaveDataTransformSchemaProcessor::class,
                'integer' => NoOpPreSaveDataTransformSchemaProcessor::class,
                'list' => DynamicListPreSaveDataTransformSchemaProcessor::class,
                'map' => DynamicListPreSaveDataTransformSchemaProcessor::class,
                'string' => NoOpPreSaveDataTransformSchemaProcessor::class,
                'switch' => SwitchPreSaveDataTransformSchemaProcessor::class,
            ],
            ConvertValueTypesSchemaProcessorInterface::class => [
                'boolean' => BooleanConvertValueTypesSchemaProcessor::class,
                'container' => ContainerConvertValueTypesSchemaProcessor::class,
                'custom' => CustomConvertValueTypesSchemaProcessor::class,
                'integer' => IntegerConvertValueTypesSchemaProcessor::class,
                'list' => DynamicListConvertValueTypesSchemaProcessor::class,
                'map' => DynamicListConvertValueTypesSchemaProcessor::class,
                'string' => StringConvertValueTypesSchemaProcessor::class,
                'switch' => SwitchConvertValueTypesSchemaProcessor::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    protected const FRONTEND_SCRIPTS = [
        'core' => [
            '/scripts/digital-marketing-framework.js',
        ],
    ];

    protected function getGlobalConfigurationSchema(): ?SchemaInterface
    {
        $schema = new ContainerSchema();
        $schema->getRenderingDefinition()->setLabel('Core');

        $schema->addProperty('debug', new BooleanSchema(false));

        $configurationStorageSchema = new ContainerSchema();
        $configurationStorageSchema->getRenderingDefinition()->setNavigationItem(false);

        $configurationStorageSchema->addProperty('folder', new StringSchema());

        $defaultConfigurationDocumentSchema = new StringSchema();
        $defaultConfigurationDocumentSchema->getAllowedValues()->addValue('', '-- NONE --');
        $defaultConfigurationDocumentSchema->getAllowedValues()->addValueSet('document/all');
        $defaultConfigurationDocumentSchema->getRenderingDefinition()->setFormat(RenderingDefinitionInterface::FORMAT_SELECT);
        $configurationStorageSchema->addProperty('defaultConfigurationDocument', $defaultConfigurationDocumentSchema);

        $configurationStorageSchema->addProperty('allowSaveToExtensionPaths', new BooleanSchema(false));

        $schema->addProperty('configurationStorage', $configurationStorageSchema);

        $apiSchema = new ContainerSchema();
        $apiSchema->getRenderingDefinition()->setLabel('API');
        $apiSchema->getRenderingDefinition()->setNavigationItem(false);

        $apiSchema->addProperty('enabled', new BooleanSchema(false));
        $apiSchema->addProperty('basePath', new StringSchema('digital-marketing-framework/api'));
        $apiSchema->addProperty('pid', new IntegerSchema(0));

        $schema->addProperty('api', $apiSchema);

        return $schema;
    }

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('core', '1.0.0', $packageAlias);
    }
}
