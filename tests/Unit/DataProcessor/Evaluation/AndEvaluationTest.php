<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation;

class AndEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = AndEvaluation::class;
    protected const KEYWORD = 'and';

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
     * @test
     * @dataProvider andDataProvider
     */
    public function and(bool $expectedResult, array $config, array $subResults): void
    {
        $with = array_map(function(array $subConfigItem) { return [$subConfigItem]; }, $config);
        if (!empty($config)) {
            $this->dataProcessor->expects($this->exactly(count($config)))->method('processEvaluation')->withConsecutive(...$with)->willReturn(...$subResults);
        }
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
