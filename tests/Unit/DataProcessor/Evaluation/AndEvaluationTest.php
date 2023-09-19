<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation;

class AndEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = AndEvaluation::class;

    protected const KEYWORD = 'and';

    /**
     * @return array<array{0:bool,1:array<array<string,mixed>>,2:array<mixed>}>
     */
    public function andDataProvider(): array
    {
        return [
            [true, [], []],

            [true, [['a' => 'b']], [true]],
            [false, [['a' => 'b']], [false]],

            [true, [['a' => 'b'], ['c' => 'd']], [true, true]],
            [false, [['a' => 'b'], ['c' => 'd']], [true, false]],
            [false, [['a' => 'b'], ['c' => 'd']], [false, true]],
            [false, [['a' => 'b'], ['c' => 'd']], [false, false]],

            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, false, false]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, false, true]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, true, false]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [false, true, true]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, false, false]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, false, true]],
            [false, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, true, false]],
            [true, [['a' => 'b'], ['c' => 'd'], ['e' => 'f']], [true, true, true]],
        ];
    }

    /**
     * @param array<array<string,mixed>> $subConfigList
     * @param array<mixed> $subResults
     *
     * @test
     *
     * @dataProvider andDataProvider
     */
    public function and(bool $expectedResult, array $subConfigList, array $subResults): void
    {
        $with = array_map(static function (array $subConfigItem) {
            return [$subConfigItem];
        }, $subConfigList);
        if ($subConfigList !== []) {
            $this->dataProcessor->expects($this->exactly(count($subConfigList)))->method('processEvaluation')->withConsecutive(...$with)->willReturn(...$subResults);
        }

        $config = [
            AndEvaluation::KEY_EVALUATIONS => [],
        ];
        $id = 1;
        foreach ($subConfigList as $subConfigItem) {
            $config[AndEvaluation::KEY_EVALUATIONS]['id' . $id] = $this->createListItem($subConfigItem, 'id' . $id, $id * 10);
            ++$id;
        }

        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
