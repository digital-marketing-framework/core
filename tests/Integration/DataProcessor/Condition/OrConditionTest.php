<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\OrCondition;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Condition\OrCondition
 */
class OrConditionTest extends ConditionTest
{
    protected const KEYWORD = 'or';

    /**
     * @return array<array{0:bool,1:array<string,mixed>}>
     */
    public function orDataProvider(): array
    {
        return [
            [true, []],

            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
                    'id1' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getConditionConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getConditionConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrCondition::KEY_CONDITIONS => [
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
     * @dataProvider orDataProvider
     */
    public function or(bool $expectedResult, array $config): void
    {
        $result = $this->processCondition($config);
        $this->assertEquals($expectedResult, $result);
    }
}
