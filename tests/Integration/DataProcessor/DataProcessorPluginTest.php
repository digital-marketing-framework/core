<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\Tests\Integration\CoreRegistryTestTrait;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\TestCase;

abstract class DataProcessorPluginTest extends TestCase
{
    use CoreRegistryTestTrait;
    use MultiValueTestTrait;
    use ListMapTestTrait;

    protected const KEYWORD = '';

    protected function setUp(): void
    {
        $this->initRegistry();
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array{type:string,config:array<string,array<string,mixed>>}
     */
    protected function getValueSourceConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword ??= static::KEYWORD;

        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array{type:string,config:array<string,array<string,mixed>>}
     */
    protected function getValueModifierConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword ??= static::KEYWORD;

        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    /**
     * TODO shouldn't the modifiers config array be a list of modifiers instead of just one?
     *
     * @param array<string,mixed> $valueSourceConfig
     * @param ?array<string,mixed> $modifierConfig
     *
     * @return array{
     *   data:array{type:string,config:array<string,array<string,mixed>>},
     *   modifiers:array{type:string,config:array<string,array<string,mixed>>}
     * }
     */
    protected function getValueConfiguration(array $valueSourceConfig, string $valueSourceKeyword, ?array $modifierConfig = null, ?string $modifierKeyword = null): array
    {
        return [
            DataProcessor::KEY_DATA => $this->getValueSourceConfiguration($valueSourceConfig, $valueSourceKeyword),
            DataProcessor::KEY_MODIFIERS => $modifierConfig !== null ? $this->getValueModifierConfiguration($modifierConfig, $modifierKeyword) : [],
        ];
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array{type:string,config:array<string,array<string,mixed>>}
     */
    protected function getEvaluationConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword ??= static::KEYWORD;

        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $firstOperandConfig
     * @param ?array<string,mixed> $secondOperandConfig
     *
     * @return array{
     *   type:string,
     *   firstOperand:array<string,mixed>,
     *   secondOperand?:array<string,mixed>,
     *   anyAll?:string
     * }
     */
    protected function getComparisonConfiguration(array $firstOperandConfig, ?array $secondOperandConfig = null, ?string $anyAllConfig = null, ?string $keyword = null): array
    {
        $keyword ??= static::KEYWORD;
        $config = [
            Comparison::KEY_OPERATION => $keyword,
            BinaryComparison::KEY_FIRST_OPERAND => $firstOperandConfig,
        ];
        if ($secondOperandConfig !== null) {
            $config[BinaryComparison::KEY_SECOND_OPERAND] = $secondOperandConfig;
        }

        if ($anyAllConfig !== null) {
            $config[Comparison::KEY_ANY_ALL] = $anyAllConfig;
        }

        return $config;
    }

    /**
     * @param array<string,mixed> $config
     *
     * @return array<string,array<string,mixed>>
     */
    protected function getDataMapperConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword ??= static::KEYWORD;

        return [
            $keyword => $config,
        ];
    }
}
