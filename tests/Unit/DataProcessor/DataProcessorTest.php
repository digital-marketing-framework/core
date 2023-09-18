<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\Evaluation\EvaluationInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataProcessorTest extends TestCase
{
    use ListMapTestTrait;

    protected RegistryInterface&MockObject $registry;

    /**
     * @var array<ValueSourceInterface&MockObject>
     */
    protected array $valueSources = [];

    /**
     * @var array<ValueModifierInterface&MockObject>
     */
    protected array $valueModifiers = [];

    /**
     * @var array<ComparisonInterface&MockObject>
     */
    protected array $comparisons = [];

    /**
     * @var array<EvaluationInterface&MockObject>
     */
    protected array $evaluations = [];

    /**
     * @var array<DataMapperInterface&MockObject>
     */
    protected array $dataMappers = [];

    protected DataProcessorContextInterface&MockObject $context;

    protected DataProcessor $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(RegistryInterface::class);

        $this->registry->method('getValueSource')->will($this->returnCallback(function (string $keyword, array $config) {
            if (!isset($this->valueSources[$keyword])) {
                return null;
            }

            if (isset($this->valueSources[$keyword]['config'])) {
                $this->assertEquals($this->valueSources[$keyword]['config'], $config);
            }

            return $this->valueSources[$keyword]['object'];
        }));

        $this->registry->method('getComparison')->will($this->returnCallback(function (string $keyword, array $config) {
            if (!isset($this->comparisons[$keyword])) {
                return null;
            }

            if (isset($this->comparisons[$keyword]['config'])) {
                $this->assertEquals($this->comparisons[$keyword]['config'], $config);
            }

            return $this->comparisons[$keyword]['object'];
        }));

        $this->registry->method('getEvaluation')->will($this->returnCallback(function (string $keyword, array $config) {
            if (!isset($this->evaluations[$keyword])) {
                return null;
            }

            if (isset($this->evaluations[$keyword]['config'])) {
                $this->assertEquals($this->evaluations[$keyword]['confgig'], $config);
            }

            return $this->evaluations[$keyword]['object'];
        }));

        $this->registry->method('getDataMapper')->will($this->returnCallback(function (string $keyword, array $config) {
            if (!isset($this->dataMappers[$keyword])) {
                return null;
            }

            if (isset($this->dataMappers[$keyword]['config'])) {
                $this->assertEquals($this->dataMappers[$keyword]['config'], $config);
            }

            return $this->dataMappers[$keyword]['object'];
        }));

        $this->registry->method('getValueModifier')->will($this->returnCallback(function (string $keyword, array $config) {
            if (!isset($this->valueModifiers[$keyword])) {
                return null;
            }

            if (isset($this->valueModifiers[$keyword]['config'])) {
                $this->assertEquals($this->valueModifiers[$keyword]['config'], $config);
            }

            return $this->valueModifiers[$keyword]['object'];
        }));

        $this->context = $this->createMock(DataProcessorContextInterface::class);

        $this->subject = new DataProcessor($this->registry);
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addDataMapper(string $keyword, DataInterface $return, ?array $config = null, mixed $with = null): DataMapperInterface&MockObject
    {
        $dataMapper = $this->createMock(DataMapperInterface::class);
        if ($with !== null) {
            $dataMapper->method('mapData')->with($with)->willReturn($return);
        } else {
            $dataMapper->method('mapData')->willReturn($return);
        }

        $this->dataMappers[$keyword]['object'] = $dataMapper;
        if ($config !== null) {
            $this->dataMappers[$keyword]['config'] = $config;
        }

        return $dataMapper;
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addValueSource(string $keyword, string|null|ValueInterface $return, ?array $config = null, mixed $with = null): ValueSourceInterface&MockObject
    {
        $valueSource = $this->createMock(ValueSourceInterface::class);
        if ($with !== null) {
            $valueSource->method('build')->with($with)->willReturn($return);
        } else {
            $valueSource->method('build')->willReturn($return);
        }

        $this->valueSources[$keyword]['object'] = $valueSource;
        if ($config !== null) {
            $this->valueSources[$keyword]['config'] = $config;
        }

        return $valueSource;
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addValueModifier(string $keyword, string|null|ValueInterface $return, ?array $config = null, mixed $with = null): ValueModifierInterface&MockObject
    {
        $valueModifier = $this->createMock(ValueModifierInterface::class);
        if ($with !== null) {
            $valueModifier->method('modify')->with($with)->willReturn($return);
        } else {
            $valueModifier->method('modify')->willReturn($return);
        }

        $this->valueModifiers[$keyword]['object'] = $valueModifier;
        if ($config !== null) {
            $this->valueModifiers[$keyword]['config'] = $config;
        }

        return $valueModifier;
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addComparison(string $keyword, bool $return, ?array $config = null, mixed $with = null): ComparisonInterface&MockObject
    {
        $comparison = $this->createMock(ComparisonInterface::class);
        if ($with !== null) {
            $comparison->method('compare')->with($with)->willReturn($return);
        } else {
            $comparison->method('compare')->willReturn($return);
        }

        $this->comparisons[$keyword]['object'] = $comparison;
        if ($config !== null) {
            $this->comparisons['config'] = $config;
        }

        return $comparison;
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addEvaluation(string $keyword, bool $return, ?array $config = null, mixed $with = null): EvaluationInterface&MockObject
    {
        $evaluation = $this->createMock(EvaluationInterface::class);
        if ($with !== null) {
            $evaluation->method('evaluate')->with($with)->willReturn($return);
        } else {
            $evaluation->method('evaluate')->willReturn($return);
        }

        $this->evaluations[$keyword]['object'] = $evaluation;
        if ($config !== null) {
            $this->evaluations['config'] = $config;
        }

        return $evaluation;
    }

    /**
     * @return array<array<bool>>
     */
    public function trueFalseDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /** @test */
    public function dataMapperEmptyConfigurationProducesEmptyData(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [];
        $output = $this->subject->processDataMapper($config, new Data(), new Configuration([[]]));
        $this->assertTrue($output instanceof DataInterface);
        $this->assertEmpty($output->toArray());
    }

    /** @test */
    public function nonExistentDataMapperWillThrowException(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [
            'notExistent' => ['configKeyA' => 'configValueA'],
        ];
        $this->expectExceptionMessage('DataMapper "notExistent" not found.');
        $this->subject->processDataMapper($config, new Data(), new Configuration([[]]));
    }

    /** @test */
    public function existentDataMapperWillBeUsed(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [
            'testMapper' => ['configKeyA' => 'configKeyB'],
        ];
        $output = $this->subject->processDataMapper($config, new Data(), new Configuration([[]]));
        $this->assertEquals(['foo' => 'bar'], $output->toArray());
    }

    /** @test */
    public function multipleDataMappersWillBeUsedConsequtively(): void
    {
        $data0 = new Data(['field_x' => 'value_x']);
        $data1 = new Data(['foo' => 'bar']);
        $data2 = new Data(['abc' => 'xyz']);

        $mapperConfig1 = [];
        $mapperConfig2 = [];

        $this->addDataMapper('mapper1', $data1, $mapperConfig1, new Data());
        $this->addDataMapper('mapper2', $data2, $mapperConfig2, $data1);

        $config = [
            'mapper1' => $mapperConfig1,
            'mapper2' => $mapperConfig2,
        ];
        $output = $this->subject->processDataMapper($config, $data0, new Configuration([[]]));
        $this->assertEquals(['abc' => 'xyz'], $output->toArray());
    }

    /** @test */
    public function valueSourceEmptyConfigurationWillThrowException(): void
    {
        $this->addValueSource('testSource', 'foo');
        $config = [];
        $this->expectExceptionMessage('no switch type found');
        $this->subject->processValueSource($config, $this->context);
    }

    /** @test */
    public function nonExistentValueSourceWillThrowException(): void
    {
        $this->addValueSource('testSource', 'foo');
        $config = [
            'type' => 'nonExistentSource',
            'config' => [
                'nonExistentSource' => [],
            ],
        ];
        $this->expectExceptionMessage('ValueSource "nonExistentSource" not found.');
        $this->subject->processValueSource($config, $this->context);
    }

    /** @test */
    public function existentValueSourceWillBeUsed(): void
    {
        $this->addValueSource('testSource', 'foo');
        $config = [
            'type' => 'testSource',
            'config' => [
                'testSource' => [],
            ],
        ];
        $output = $this->subject->processValueSource($config, $this->context);
        $this->assertEquals('foo', $output);
    }

    /** @test */
    public function valueModifierEmptyConfigurationWillReturnOriginalValue(): void
    {
        $this->addValueModifier('testModifier', 'testModifiedValue');
        $config = [];
        $output = $this->subject->processValueModifiers($config, 'foo', $this->context);
        $this->assertEquals('foo', $output);
    }

    /** @test */
    public function nonExistentValueModifierWillThrowException(): void
    {
        $this->addValueModifier('testModifier', 'testModifierValue');
        $config = [
            'id1' => $this->createListItem(
                [
                    'type' => 'nonExistentModifier',
                    'config' => [
                        'nonExistentModifier' => ['modifierConfigKey' => 'modifierConfigValue'],
                    ],
                ],
                'id1',
                10
            ),
        ];
        $this->expectExceptionMessage('ValueModifier "nonExistentModifier" not found.');
        $this->subject->processValueModifiers($config, 'foo', $this->context);
    }

    /** @test */
    public function existentValueModifierWillBeUsed(): void
    {
        $this->addValueModifier('testModifier', 'testModifierValue');
        $config = [
            'id1' => $this->createListItem(
                [
                    'type' => 'testModifier',
                    'config' => [
                        'testModifier' => ['modifierConfigKey' => 'modifierConfigValue'],
                    ],
                ],
                'id1',
                10
            ),
        ];
        $output = $this->subject->processValueModifiers($config, 'foo', $this->context);
        $this->assertEquals('testModifierValue', $output);
    }

    /** @test */
    public function multipleValueModifiersWillBeUsedConsequtively(): void
    {
        $value0 = 'foo';
        $value1 = 'bar';
        $value2 = 'baz';

        $modifierConfig1 = [];
        $modifierConfig2 = [];

        $this->addValueModifier('modifier1', $value1, $modifierConfig1, $value0);
        $this->addValueModifier('modifier2', $value2, $modifierConfig2, $value1);

        $config = [
            'id1' => $this->createListItem(
                [
                    'type' => 'modifier1',
                    'config' => [
                        'modifier1' => $modifierConfig1,
                    ],
                ],
                'id1',
                10
            ),
            'id2' => $this->createListItem(
                [
                    'type' => 'modifier2',
                    'config' => [
                        'modifier2' => $modifierConfig2,
                    ],
                ],
                'id2',
                20
            ),
        ];
        $output = $this->subject->processValueModifiers($config, $value0, $this->context);
        $this->assertEquals('baz', $output);
    }

    /** @test */
    public function valueWithEmptyConfigurationWillThrowException(): void
    {
        $this->addValueSource('testSource', 'foo');
        $this->addValueModifier('testModifier', 'bar');
        $config = [];
        $this->expectExceptionMessage('No data for value source configuration found.');
        $this->subject->processValue($config, $this->context);
    }

    /** @test */
    public function valueWithValueSourceWithoutModifiersWillReturnOriginalValue(): void
    {
        $this->addValueSource('testSource', 'foo', ['testSourceConfigKey' => 'testSourceConfigValue']);
        $this->addValueModifier('testModifier', 'bar');
        $config = [
            DataProcessor::KEY_DATA => [
                'type' => 'testSource',
                'config' => [
                    'testSource' => [
                        'testSourceConfigKey' => 'testSourceConfigValue',
                    ],
                ],
            ],
            DataProcessor::KEY_MODIFIERS => [],
        ];
        $output = $this->subject->processValue($config, $this->context);
        $this->assertEquals('foo', $output);
    }

    /** @test */
    public function valueWithValueSourceAndValueModifiersWillReturnModifiedValue(): void
    {
        $this->addValueSource('testSource', 'foo', ['testSourceConfigKey' => 'testSourceConfigValue']);
        $this->addValueModifier('testModifier1', 'bar', ['testModifier1ConfigKey' => 'testModifier1ConfigValue'], 'foo');
        $this->addValueModifier('testModifier2', 'baz', ['testModifier2ConfigKey' => 'testModifier2ConfigValue'], 'bar');
        $config = [
            DataProcessor::KEY_DATA => [
                'type' => 'testSource',
                'config' => [
                    'testSource' => [
                        'testSourceConfigKey' => 'testSourceConfigValue',
                    ],
                ],
            ],
            DataProcessor::KEY_MODIFIERS => [
                'id1' => $this->createListItem([
                    'type' => 'testModifier1',
                    'config' => [
                        'testModifier1' => ['testModifier1ConfigKey' => 'testModifier1ConfigValue'],
                    ],
                ], 'id1', 10),
                'id2' => $this->createListItem([
                    'type' => 'testModifier2',
                    'config' => [
                        'testModifier2' => ['testModifier2ConfigKey' => 'testModifier2ConfigValue'],
                    ],
                ], 'id2', 20),
            ],
        ];
        $output = $this->subject->processValue($config, $this->context);
        $this->assertEquals('baz', $output);
    }

    /** @test */
    public function evaluationEmptyConfigurationWillThrowException(): void
    {
        $config = [];
        $this->expectExceptionMessage('no switch type found');
        $this->subject->processEvaluation($config, $this->context);
    }

    /** @test */
    public function nonExistentEvaluationWillThrowException(): void
    {
        $this->addEvaluation('testEvaluation', true);
        $config = [
            'type' => 'nonExistentEvaluation',
            'config' => [
                'nonExistentEvaluation' => [],
            ],
        ];
        $this->expectExceptionMessage('Evaluation "nonExistentEvaluation" not found.');
        $this->subject->processEvaluation($config, $this->context);
    }

    /**
     * @test
     *
     * @dataProvider trueFalseDataProvider
     */
    public function existentEvaluationWillBeUsed(bool $expectedResult): void
    {
        $this->addEvaluation('testEvaluation', $expectedResult, ['evaluationConfigKey' => 'evaluationConfigValue']);
        $config = [
            'type' => 'testEvaluation',
            'config' => [
                'testEvaluation' => ['evaluationConfigKey' => 'evaluationConfigValue'],
            ],
        ];
        $output = $this->subject->processEvaluation($config, $this->context);
        $this->assertEquals($expectedResult, $output);
    }

    /** @test */
    public function comparisonWithEmptyConfigurationWillThrowException(): void
    {
        $this->addComparison('testComparison', true);
        $config = [];
        $this->expectExceptionMessage('no comparison operation given');
        $this->subject->processComparison($config, $this->context);
    }

    /** @test */
    public function nonExistentComparisonWillThrowException(): void
    {
        $this->addComparison('testComparison', true);
        $config = [
            'type' => 'nonExistentComparison',
        ];
        $this->expectExceptionMessage('Comparison "nonExistentComparison" not found.');
        $this->subject->processComparison($config, $this->context);
    }

    /**
     * @test
     *
     * @dataProvider trueFalseDataProvider
     */
    public function existingComparisonWillBeUsed(bool $expectedResult): void
    {
        $this->addComparison('testComparison', $expectedResult, ['comparisonConfigKey' => 'comparisonConfigValue']);
        $config = [
            'type' => 'testComparison',
            'comparisonConfigKey' => 'comparisonConfigValue',
        ];
        $output = $this->subject->processComparison($config, $this->context);
        $this->assertEquals($expectedResult, $output);
    }
}
