<?php

namespace DigitalMarketingFramework\Core\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SwitchSchema;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\FieldMapDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\PassthroughFieldsDataMapper;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldCollectorValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\FieldValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Model\Configuration\ConfigurationInterface;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Plugin\Plugin;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Utility\GeneralUtility;
use DigitalMarketingFramework\Core\Utility\ListUtility;

class DataProcessor extends Plugin implements DataProcessorInterface
{
    public const KEY_DATA = 'data';

    public const KEY_MODIFIERS = 'modifiers';

    public function __construct(
        protected RegistryInterface $registry
    ) {
    }

    public function createContext(DataInterface $data, ConfigurationInterface $configuration): DataProcessorContextInterface
    {
        return new DataProcessorContext($data, $configuration);
    }

    public function processValueSource(array $config, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        $keyword = SwitchSchema::getSwitchType($config);
        $valueSourceConfig = SwitchSchema::getSwitchConfiguration($config);
        $valueSource = $this->registry->getValueSource($keyword, $valueSourceConfig, $context);
        if (!$valueSource instanceof ValueSourceInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('ValueSource "%s" not found.', $keyword));
        }

        return $valueSource->build();
    }

    public function processValueModifiers(array $config, string|ValueInterface|null $value, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        $config = ListUtility::flatten($config);
        foreach ($config as $modifierSwitchConfig) {
            $keyword = SwitchSchema::getSwitchType($modifierSwitchConfig);
            $modifierConfig = SwitchSchema::getSwitchConfiguration($modifierSwitchConfig);
            $modifier = $this->registry->getValueModifier($keyword, $modifierConfig, $context);
            if (!$modifier instanceof ValueModifierInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('ValueModifier "%s" not found.', $keyword));
            }

            $value = $modifier->modify($value);
        }

        return $value;
    }

    public function processValue(array $config, DataProcessorContextInterface $context): string|ValueInterface|null
    {
        // build
        $dataConfig = $config[static::KEY_DATA] ?? null;
        if ($dataConfig === null) {
            throw new DigitalMarketingFrameworkException('No data for value source configuration found.');
        }

        $value = $this->processValueSource($dataConfig, $context);

        // modify
        $modifierConfig = $config[static::KEY_MODIFIERS] ?? null;
        if ($modifierConfig === null) {
            throw new DigitalMarketingFrameworkException('No data for value modifiers configuration found.');
        }

        return $this->processValueModifiers($modifierConfig, $value, $context);
    }

    public function processComparison(array $config, DataProcessorContextInterface $context): bool
    {
        $keyword = $config[Comparison::KEY_OPERATION] ?? null;
        if ($keyword === null) {
            throw new DigitalMarketingFrameworkException('no comparison operation given');
        }

        $comparison = $this->registry->getComparison($keyword, $config, $context);
        if (!$comparison instanceof ComparisonInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('Comparison "%s" not found.', $keyword));
        }

        return $comparison->compare();
    }

    public function processEvaluation(array $config, DataProcessorContextInterface $context): bool
    {
        $keyword = SwitchSchema::getSwitchType($config);
        $evaluationConfig = SwitchSchema::getSwitchConfiguration($config);
        $evaluation = $this->registry->getEvaluation($keyword, $evaluationConfig, $context);
        if (!$evaluation instanceof EvaluationInterface) {
            throw new DigitalMarketingFrameworkException(sprintf('Evaluation "%s" not found.', $keyword));
        }

        return $evaluation->evaluate();
    }

    public function processDataMapper(array $config, DataInterface $data, ConfigurationInterface $configuration): DataInterface
    {
        $context = $this->createContext($data, $configuration);
        $target = new Data();
        foreach ($config as $keyword => $dataMapperConfig) {
            $dataMapper = $this->registry->getDataMapper($keyword, $dataMapperConfig, $context);
            if (!$dataMapper instanceof DataMapperInterface) {
                throw new DigitalMarketingFrameworkException(sprintf('DataMapper "%s" not found.', $keyword));
            }

            $target = $dataMapper->mapData($target);
        }

        return $target;
    }

    /**
     * @param array<string,mixed> $dataMapperConfig
     *
     * @return array<string,mixed>
     */
    public static function dataMapperSchemaDefaultValuePassthroughFields(array $dataMapperConfig = []): array
    {
        $keyword = GeneralUtility::getPluginKeyword(PassthroughFieldsDataMapper::class, DataMapperInterface::class);
        $dataMapperConfig[$keyword] = [
            PassthroughFieldsDataMapper::KEY_ENABLED => true,
        ];

        return $dataMapperConfig;
    }

    /**
     * @param array<string,mixed> $fields
     * @param array<string,mixed> $dataMapperConfig
     *
     * @return array<string,mixed>
     */
    public static function dataMapperSchemaDefaultValueFieldMap(array $fields, array $dataMapperConfig = []): array
    {
        $keyword = GeneralUtility::getPluginKeyword(FieldMapDataMapper::class, DataMapperInterface::class);
        $dataMapperConfig[$keyword] = [
            FieldMapDataMapper::KEY_FIELDS => $fields,
        ];

        return $dataMapperConfig;
    }

    /**
     * @return array{data:array{type:string,config:array<string,array{fieldName:string}>}}
     */
    public static function valueSchemaDefaultValueField(string $fieldName): array
    {
        $keyword = GeneralUtility::getPluginKeyword(FieldValueSource::class, ValueSourceInterface::class);

        return [
            static::KEY_DATA => [
                SwitchSchema::KEY_TYPE => $keyword,
                SwitchSchema::KEY_CONFIG => [
                    $keyword => [
                        FieldValueSource::KEY_FIELD_NAME => $fieldName,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{data:array{type:string,config:array<string,array{value:string}>}}
     */
    public static function valueSchemaDefaultValueConstant(string $constantValue): array
    {
        $keyword = GeneralUtility::getPluginKeyword(ConstantValueSource::class, ValueSourceInterface::class);

        return [
            static::KEY_DATA => [
                SwitchSchema::KEY_TYPE => $keyword,
                SwitchSchema::KEY_CONFIG => [
                    $keyword => [
                        ConstantValueSource::KEY_VALUE => $constantValue,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{data:array{type:string}}
     */
    public static function valueSchemaDefaultValueNull(): array
    {
        $keyword = GeneralUtility::getPluginKeyword(NullValueSource::class, ValueSourceInterface::class);

        return [
            static::KEY_DATA => [
                SwitchSchema::KEY_TYPE => $keyword,
            ],
        ];
    }

    /**
     * @return array{data:array{type:string,config:array<string,array{unprocessedOnly:bool}>}}
     */
    public static function valueSchemaDefaultValueFieldCollector(): array
    {
        $keyword = GeneralUtility::getPluginKeyword(FieldCollectorValueSource::class, ValueSourceInterface::class);

        return [
            static::KEY_DATA => [
                SwitchSchema::KEY_TYPE => $keyword,
                SwitchSchema::KEY_CONFIG => [
                    $keyword => [
                        // TODO setting this variable to its default value is currently necessary, just so that the array is not empty.
                        //      empty arrays in schema default values are currently not processed before translated to YAML or JSON,
                        //      so this config as empty array would become "[]" instead of "{}".
                        //      the big challenge is that the default value is part of the schema,
                        //      not part of an actual configuration, which does get processed correctly.
                        //      we might need an equivalent of Schema::preSaveDataTransform() for schema defaults
                        //      preSaveSchemaDefaultDataTransform()?
                        FieldCollectorValueSource::KEY_UNPROCESSED_ONLY => FieldCollectorValueSource::DEFAULT_UNPROCESSED_ONLY,
                    ],
                ],
            ],
        ];
    }
}
