<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use PHPUnit\Framework\TestCase;

use DigitalMarketingFramework\Core\Tests\Integration\CoreRegistryTestTrait;

abstract class DataProcessorPluginTest extends TestCase
{
    use CoreRegistryTestTrait;
    use MultiValueTestTrait;
    use ListMapTestTrait;

    protected const KEYWORD = '';

    public function setUp(): void
    {
        $this->initRegistry();
    }

    protected function getValueSourceConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword = $keyword ?? static::KEYWORD;
        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    protected function getValueModifierConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword = $keyword ?? static::KEYWORD;
        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    protected function getValueConfiguration(array $valueSourceConfig, string $valueSourceKeyword, ?array $modifierConfig = null, ?string $modifierKeyword = null): array
    {
        return [
            DataProcessor::KEY_DATA => $this->getValueSourceConfiguration($valueSourceConfig, $valueSourceKeyword),
            DataProcessor::KEY_MODIFIERS => $modifierConfig !== null ? $this->getValueModifierConfiguration($modifierConfig, $modifierKeyword) : [],
        ];
    }

    protected function getEvaluationConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword = $keyword ?? static::KEYWORD;
        return [
            'type' => $keyword,
            'config' => [
                $keyword => $config,
            ],
        ];
    }

    protected function getComparisonConfiguration(array $firstOperatorConfig, ?array $secondOperatorConfig = null, ?string $anyAllConfig = null, ?string $keyword = null): array
    {
        $keyword = $keyword ?? static::KEYWORD;
        $config = [
            Comparison::KEY_OPERATION => $keyword,
            BinaryComparison::KEY_FIRST_OPERAND => $firstOperatorConfig,
        ];
        if ($secondOperatorConfig !== null) {
            $config[BinaryComparison::KEY_SECOND_OPERAND] = $secondOperatorConfig;
        }
        if ($anyAllConfig !== null) {
            $config[Comparison::KEY_ANY_ALL] = $anyAllConfig;
        }
        return $config;
    }

    protected function getDataMapperConfiguration(array $config, ?string $keyword = null): array
    {
        $keyword = $keyword ?? static::KEYWORD;
        return [
            $keyword => $config,
        ];
    }
}
