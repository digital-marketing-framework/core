<?php

namespace DigitalMarketingFramework\Core;

use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\GlobalSettingsConfigurationEditorAjaxController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\ApiSectionController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\ConfigurationDocumentSectionController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\DashboardSectionController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\GlobalSettingsSectionController;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;
use DigitalMarketingFramework\Core\Backend\Section\Section;
use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticCoreSystemConfigurationDocumentDiscovery;
use DigitalMarketingFramework\Core\ConfigurationDocument\Discovery\StaticResourceConfigurationDocumentDiscovery;
use DigitalMarketingFramework\Core\DataPrivacy\UnregulatedDataPrivacyPlugin;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\EqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ExistsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\InComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsEmptyComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsFalseComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\IsTrueComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\NotEqualsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\NotExistsComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\NotInComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\NotIsEmptyComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\NotRegExpComparison;
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
use DigitalMarketingFramework\Core\GlobalConfiguration\Schema\CoreGlobalConfigurationSchema;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
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
                NotEqualsComparison::class,
                ExistsComparison::class,
                NotExistsComparison::class,
                InComparison::class,
                NotInComparison::class,
                IsEmptyComparison::class,
                NotIsEmptyComparison::class,
                IsTrueComparison::class,
                IsFalseComparison::class,
                RegExpComparison::class,
                NotRegExpComparison::class,
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

            // backend
            SectionControllerInterface::class => [
                DashboardSectionController::class,
                ConfigurationDocumentSectionController::class,
                GlobalSettingsSectionController::class,
                ApiSectionController::class,
            ],
            AjaxControllerInterface::class => [
                GlobalSettingsConfigurationEditorAjaxController::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    protected const FRONTEND_SCRIPTS = [
        'core' => [
            'digital-marketing-framework.js',
        ],
    ];

    protected function getBackendSections(): array
    {
        return [
            new Section(
                'Global Settings',
                'CORE',
                'page.global-settings.edit',
                'Manage Global Settings',
                'PKG:digital-marketing-framework/core/res/assets/icons/dashboard-global-settings.svg',
                'Show',
                50
            ),
            new Section(
                'Configuration',
                'CORE',
                'page.configuration-document.list',
                'Manage Configuration Documents',
                'PKG:digital-marketing-framework/core/res/assets/icons/dashboard-configuration.svg',
                'Show',
                100
            ),
            new Section(
                'API',
                'CORE',
                'page.api.list',
                'Manage Personalization API',
                'PKG:digital-marketing-framework/core/res/assets/icons/dashboard-api.svg',
                'Show',
                100
            ),
            new Section( // TODO move to package notification-db
                'Notifications',
                'CORE',
                'page.notification.list',
                'Read and manage Notifications',
                'PKG:digital-marketing-framework/core/res/assets/icons/dashboard-notifications.svg',
                'Show',
                100
            ),
            new Section(
                'Tests',
                'CORE',
                'page.tests.list',
                'Project Setup Test Suite',
                'PKG:digital-marketing-framework/core/res/assets/icons/dashboard-tests.svg',
                'Show',
                100
            ),
        ];
    }

    public function initPlugins(string $domain, RegistryInterface $registry): void
    {
        parent::initPlugins($domain, $registry);

        $registry->registerResourceService($registry->getVendorResourceService());

        $registry->registerStaticConfigurationDocumentDiscovery(
            $registry->createObject(StaticResourceConfigurationDocumentDiscovery::class, [$registry])
        );

        $registry->registerStaticConfigurationDocumentDiscovery(
            $registry->createObject(StaticCoreSystemConfigurationDocumentDiscovery::class, [$registry])
        );

        $enableUnregulatedDataPrivacyPlugin = $registry->getGlobalConfiguration()->get('core', [])[CoreGlobalConfigurationSchema::KEY_DATA_PRIVACY][CoreGlobalConfigurationSchema::KEY_DATA_PRIVACY_ENABLE_UNREGULATED] ?? false;
        if ($enableUnregulatedDataPrivacyPlugin) {
            $registry->getDataPrivacyManager()->addPlugin(
                $registry->createObject(UnregulatedDataPrivacyPlugin::class)
            );
        }
    }

    public function __construct(string $packageAlias = '')
    {
        parent::__construct('core', '1.0.0', $packageAlias, new CoreGlobalConfigurationSchema());
    }
}
