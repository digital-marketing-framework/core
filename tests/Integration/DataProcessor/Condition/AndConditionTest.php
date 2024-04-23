<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\AndCondition;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Condition\AndCondition
 */
class AndConditionTest extends ConditionTest
{
    protected const KEYWORD = 'and';

    /**
     * @return array<array{0:bool,1:array<string,mixed>}>
     */
    public function andDataProvider(): array
    {
        return [
            [true, []],

            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                AndCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
        ];
    }

    /**
     * @param array<string,mixed> $config
     *
     * @test
     *
     * @dataProvider andDataProvider
     */
    public function and(bool $expectedResult, array $config): void
    {
        $result = $this->processCondition($config);
        $this->assertEquals($expectedResult, $result);
    }
}
