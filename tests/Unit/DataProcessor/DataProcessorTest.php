<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor;

use DigitalMarketingFramework\Core\DataProcessor\Comparison\ComparisonInterface;
use DigitalMarketingFramework\Core\DataProcessor\Condition\ConditionInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataMapper\DataMapperInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessor;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueModifier\ValueModifierInterface;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ValueSourceInterface;
use DigitalMarketingFramework\Core\Model\Configuration\Configuration;
use DigitalMarketingFramework\Core\Model\Data\Data;
use DigitalMarketingFramework\Core\Model\Data\DataInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Core\Tests\ListMapTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
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
     * @var array<array{object:ComparisonInterface&MockObject,config?:array<string,mixed>}>
     */
    protected array $comparisons = [];

    /**
     * @var array<ConditionInterface&MockObject>
     */
    protected array $conditions = [];

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

        $this->registry->method('getValueSource')->willReturnCallback(function (string $keyword, array $config) {
            if (!isset($this->valueSources[$keyword])) {
                return null;
            }

            if (isset($this->valueSources[$keyword]['config'])) {
                static::assertEquals($this->valueSources[$keyword]['config'], $config);
            }

            return $this->valueSources[$keyword]['object'];
        });

        $this->registry->method('getComparison')->willReturnCallback(function (string $keyword, array $config) {
            if (!isset($this->comparisons[$keyword])) {
                return null;
            }

            if (isset($this->comparisons[$keyword]['config'])) {
                static::assertEquals($this->comparisons[$keyword]['config'], $config);
            }

            return $this->comparisons[$keyword]['object'];
        });

        $this->registry->method('getCondition')->willReturnCallback(function (string $keyword, array $config) {
            if (!isset($this->conditions[$keyword])) {
                return null;
            }

            if (isset($this->conditions[$keyword]['config'])) {
                static::assertEquals($this->conditions[$keyword]['config'], $config);
            }

            return $this->conditions[$keyword]['object'];
        });

        $this->registry->method('getDataMapper')->willReturnCallback(function (string $keyword, array $config) {
            if (!isset($this->dataMappers[$keyword])) {
                return null;
            }

            if (isset($this->dataMappers[$keyword]['config'])) {
                static::assertEquals($this->dataMappers[$keyword]['config'], $config);
            }

            return $this->dataMappers[$keyword]['object'];
        });

        $this->registry->method('getValueModifier')->willReturnCallback(function (string $keyword, array $config) {
            if (!isset($this->valueModifiers[$keyword])) {
                return null;
            }

            if (isset($this->valueModifiers[$keyword]['config'])) {
                static::assertEquals($this->valueModifiers[$keyword]['config'], $config);
            }

            return $this->valueModifiers[$keyword]['object'];
        });

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
    protected function addValueSource(string $keyword, string|ValueInterface|null $return, ?array $config = null, mixed $with = null): ValueSourceInterface&MockObject
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
    protected function addValueModifier(string $keyword, string|ValueInterface|null $return, ?array $config = null, mixed $with = null): ValueModifierInterface&MockObject
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
            $this->comparisons[$keyword]['config'] = $config;
        }

        return $comparison;
    }

    /**
     * @param ?array<string,mixed> $config
     */
    protected function addCondition(string $keyword, bool $return, ?array $config = null, mixed $with = null): ConditionInterface&MockObject
    {
        $condition = $this->createMock(ConditionInterface::class);
        if ($with !== null) {
            $condition->method('evaluate')->with($with)->willReturn($return);
        } else {
            $condition->method('evaluate')->willReturn($return);
        }

        $this->conditions[$keyword]['object'] = $condition;
        if ($config !== null) {
            $this->conditions[$keyword]['config'] = $config;
        }

        return $condition;
    }

    /**
     * @return array<array<bool>>
     */
    public static function trueFalseDataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    #[Test]
    public function dataMapperEmptyConfigurationProducesEmptyData(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [];
        $context = $this->subject->createContext(new Data(), new Configuration([[]]));
        $output = $this->subject->processDataMapper($config, $context);
        static::assertEmpty($output->toArray());
    }

    #[Test]
    public function nonExistentDataMapperWillThrowException(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [
            'notExistent' => ['configKeyA' => 'configValueA'],
        ];
        $this->expectExceptionMessage('DataMapper "notExistent" not found.');
        $context = $this->subject->createContext(new Data(), new Configuration([[]]));
        $this->subject->processDataMapper($config, $context);
    }

    #[Test]
    public function existentDataMapperWillBeUsed(): void
    {
        $this->addDataMapper('testMapper', new Data(['foo' => 'bar']));
        $config = [
            'testMapper' => ['configKeyA' => 'configKeyB'],
        ];
        $context = $this->subject->createContext(new Data(), new Configuration([[]]));
        $output = $this->subject->processDataMapper($config, $context);
        $this->assertEquals(['foo' => 'bar'], $output->toArray());
    }

    #[Test]
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
        $context = $this->subject->createContext($data0, new Configuration([[]]));
        $output = $this->subject->processDataMapper($config, $context);
        $this->assertEquals(['abc' => 'xyz'], $output->toArray());
    }

    #[Test]
    public function valueSourceEmptyConfigurationWillThrowException(): void
    {
        $this->addValueSource('testSource', 'foo');
        $config = [];
        $this->expectExceptionMessage('no switch type found');
        $this->subject->processValueSource($config, $this->context);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function valueModifierEmptyConfigurationWillReturnOriginalValue(): void
    {
        $this->addValueModifier('testModifier', 'testModifiedValue');
        $config = [];
        $output = $this->subject->processValueModifiers($config, 'foo', $this->context);
        $this->assertEquals('foo', $output);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function valueWithEmptyConfigurationWillThrowException(): void
    {
        $this->addValueSource('testSource', 'foo');
        $this->addValueModifier('testModifier', 'bar');
        $config = [];
        $this->expectExceptionMessage('No data for value source configuration found.');
        $this->subject->processValue($config, $this->context);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function conditionEmptyConfigurationWillThrowException(): void
    {
        $config = [];
        $this->expectExceptionMessage('no switch type found');
        $this->subject->processCondition($config, $this->context);
    }

    #[Test]
    public function nonExistentConditionWillThrowException(): void
    {
        $this->addCondition('testCondition', true);
        $config = [
            'type' => 'nonExistentCondition',
            'config' => [
                'nonExistentCondition' => [],
            ],
        ];
        $this->expectExceptionMessage('Condition "nonExistentCondition" not found.');
        $this->subject->processCondition($config, $this->context);
    }

    #[Test]
    #[DataProvider('trueFalseDataProvider')]
    public function existentConditionWillBeUsed(bool $expectedResult): void
    {
        $this->addCondition('testCondition', $expectedResult, ['conditionConfigKey' => 'conditionConfigValue']);
        $config = [
            'type' => 'testCondition',
            'config' => [
                'testCondition' => ['conditionConfigKey' => 'conditionConfigValue'],
            ],
        ];
        $output = $this->subject->processCondition($config, $this->context);
        $this->assertEquals($expectedResult, $output);
    }

    #[Test]
    public function comparisonWithEmptyConfigurationWillThrowException(): void
    {
        $this->addComparison('testComparison', true);
        $config = [];
        $this->expectExceptionMessage('no comparison operation given');
        $this->subject->processComparison($config, $this->context);
    }

    #[Test]
    public function nonExistentComparisonWillThrowException(): void
    {
        $this->addComparison('testComparison', true);
        $config = [
            'type' => 'nonExistentComparison',
        ];
        $this->expectExceptionMessage('Comparison "nonExistentComparison" not found.');
        $this->subject->processComparison($config, $this->context);
    }

    #[Test]
    #[DataProvider('trueFalseDataProvider')]
    public function existingComparisonWillBeUsed(bool $expectedResult): void
    {
        $config = [
            'type' => 'testComparison',
            'comparisonConfigKey' => 'comparisonConfigValue',
        ];
        $this->addComparison('testComparison', $expectedResult, $config);
        $output = $this->subject->processComparison($config, $this->context);
        $this->assertEquals($expectedResult, $output);
    }
}
