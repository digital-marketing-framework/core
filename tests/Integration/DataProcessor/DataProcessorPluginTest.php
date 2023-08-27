<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor;

use DigitalMarketingFramework\Core\ConfigurationDocument\Parser\ConfigurationDocumentParserInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\Storage\ConfigurationDocumentStorageInterface;
use DigitalMarketingFramework\Core\CoreInitalization;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\BinaryComparison;
use DigitalMarketingFramework\Core\DataProcessor\Comparison\Comparison;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\Registry\Registry;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Tests\MultiValueTestTrait;
use DigitalMarketingFramework\Core\Tests\DataProcessorTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class DataProcessorPluginTest extends TestCase
{
    use MultiValueTestTrait;
    use DataProcessorTestTrait;

    protected const KEYWORD = '';

    protected RegistryInterface $registry;

    protected ConfigurationDocumentStorageInterface&MockObject $configurationDocumentStorage;
    protected ConfigurationDocumentParserInterface&MockObject $configurationDocumentParser;

    public function setUp(): void
    {
        $this->initRegistry();
    }

    protected function initRegistry(): void
    {
        $this->registry = new Registry();

        $this->configurationDocumentStorage = $this->createMock(ConfigurationDocumentStorageInterface::class);
        $this->registry->setConfigurationDocumentStorage($this->configurationDocumentStorage);

        $this->configurationDocumentParser = $this->createMock(ConfigurationDocumentParserInterface::class);
        $this->registry->setConfigurationDocumentParser($this->configurationDocumentParser);

        $initialization = new CoreInitalization();
        $initialization->init(RegistryDomain::CORE, $this->registry);
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
