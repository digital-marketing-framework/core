<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\AndCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(AndCondition::class)]
class AndConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'and';

    /**
     * @return array<array{0:bool,1:array<string,mixed>}>
     */
    public static function andDataProvider(): array
    {
        return [
            [true, []],

            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
        ];
    }

    /**
     * @param array<string,mixed> $config
     */
    #[Test]
    #[DataProvider('andDataProvider')]
    public function and(bool $expectedResult, array $config): void
    {
        $result = $this->processCondition($config);
        $this->assertEquals($expectedResult, $result);
    }
}
