<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\OrEvaluation;

class OrEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = OrEvaluation::class;
    protected const KEYWORD = 'or';

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
     * @test
     * @dataProvider orDataProvider
     */
    public function or(bool $expectedResult, array $config, array $subResults): void
    {
        $with = array_map(function(array $subConfigItem) { return [$subConfigItem]; }, $config);
        if (!empty($config)) {
            $this->dataProcessor->expects($this->exactly(count($config)))->method('processEvaluation')->withConsecutive(...$with)->willReturn(...$subResults);
        }
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
