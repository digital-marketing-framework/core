<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource
 */
class ConditionValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'condition';

    /**
     * @return array<array{0:array<string,mixed>,1:?array<string,mixed>,2:?array<string,mixed>,3:mixed}>
     */
    public function conditionValueSourceDataProvider(): array
    {
        return [
            [
                $this->getConditionConfiguration([], 'true'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                'value1',
            ],
            [
                $this->getConditionConfiguration([], 'true'),
                null,
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                null,
            ],
            [
                $this->getConditionConfiguration([], 'true'),
                null,
                null,
                null,
            ],
            [
                $this->getConditionConfiguration([], 'false'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                'value2',
            ],
            [
                $this->getConditionConfiguration([], 'false'),
                $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                null,
                null,
            ],
            [
                $this->getConditionConfiguration([], 'false'),
                null,
                null,
                null,
            ],
        ];
    }

    /**
     * @param array<string,mixed> $if
     * @param ?array<string,mixed> $then
     * @param ?array<string,mixed> $else
     *
     * @test
     *
     * @dataProvider conditionValueSourceDataProvider
     */
    public function conditionValueSource(array $if, ?array $then, ?array $else, mixed $expectedResult): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            ConditionValueSource::KEY_IF => $if,
            ConditionValueSource::KEY_THEN => $then,
            ConditionValueSource::KEY_ELSE => $else,
        ];
        $output = $this->processValueSource($this->getValueSourceConfiguration($config));
        $this->assertEquals($expectedResult, $output);
    }
}
