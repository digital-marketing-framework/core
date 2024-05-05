<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\OrCondition;

class OrConditionTest extends ConditionTest
{
    protected const CLASS_NAME = OrCondition::class;

    protected const KEYWORD = 'or';

    /**
     * @return array<array{0:bool,1:array<array<string,mixed>>,2:array<mixed>}>
     */
    public function orDataProvider(): array
    {
        return [
            [true, [], []],

            [true, [['a' => 'b']], [true]],
            [false, [['a' => 'b']], [false]],

            [true, [['a' => 'b'], ['c' => 'd']], [true, true]],
            [true, [['a' => 'b'], ['c' => 'd']], [true, false]],
            [true, [['a' => 'b'], ['c' => 'd']], [false, true]],
            [false, [['a' => 'b'], ['c' => 'd']], [false, false]],

            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, false, false]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, false, true]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, true, false]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, true, true]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, false, false]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, false, true]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, true, false]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, true, true]],
        ];
    }

    /**
     * @param array<array<string,mixed>> $subConfigList
     * @param array<mixed> $subResults
     *
     * @test
     *
     * @dataProvider orDataProvider
     */
    public function or(bool $expectedResult, array $subConfigList, array $subResults): void
    {
        $with = array_map(static function (array $subConfigItem) {
            return [$subConfigItem];
        }, $subConfigList);
        if ($subConfigList !== []) {
            $this->dataProcessor->expects($this->exactly(count($subConfigList)))->method('processCondition')->withConsecutive(...$with)->willReturn(...$subResults);
        }

        $config = [
            OrCondition::KEY_CONDITIONS => [],
        ];
        $id = 1;
        foreach ($subConfigList as $subConfigItem) {
            $config[OrCondition::KEY_CONDITIONS]['id' . $id] = $this->createListItem($subConfigItem, 'id' . $id, $id * 10);
            ++$id;
        }

        $result = $this->processCondition($config);
        $this->assertEquals($expectedResult, $result);
    }
}
