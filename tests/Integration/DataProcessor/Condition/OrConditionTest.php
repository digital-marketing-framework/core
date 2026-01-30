<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\OrCondition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OrCondition::class)]
class OrConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'or';

    /**
     * @return array<array{0:bool,1:array<string,mixed>}>
     */
    public static function orDataProvider(): array
    {
        return [
            [true, []],

            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => static::createListItem(static::getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => static::createListItem(static::getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
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
    #[DataProvider('orDataProvider')]
    public function or(bool $expectedResult, array $config): void
    {
        $result = $this->processCondition($config);
        self::assertEquals($expectedResult, $result);
    }
}
