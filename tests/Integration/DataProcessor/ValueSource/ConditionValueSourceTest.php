<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConditionValueSource;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ConditionValueSource::class)]
class ConditionValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'condition';

    /**
     * @return array<array{0:array<string,mixed>,1:?array<string,mixed>,2:?array<string,mixed>,3:mixed}>
     */
    public static function conditionValueSourceDataProvider(): array
    {
        return [
            [
                static::getConditionConfiguration([], 'true'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                'value1',
            ],
            [
                static::getConditionConfiguration([], 'true'),
                null,
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                null,
            ],
            [
                static::getConditionConfiguration([], 'true'),
                null,
                null,
                null,
            ],
            [
                static::getConditionConfiguration([], 'false'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
                'value2',
            ],
            [
                static::getConditionConfiguration([], 'false'),
                static::getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
                null,
                null,
            ],
            [
                static::getConditionConfiguration([], 'false'),
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
     */
    #[Test]
    #[DataProvider('conditionValueSourceDataProvider')]
    public function conditionValueSource(array $if, ?array $then, ?array $else, mixed $expectedResult): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            ConditionValueSource::KEY_IF => $if,
            ConditionValueSource::KEY_THEN => $then,
            ConditionValueSource::KEY_ELSE => $else,
        ];
        $output = $this->processValueSource(static::getValueSourceConfiguration($config));
        self::assertEquals($expectedResult, $output);
    }
}
