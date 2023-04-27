<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation;

/**
 * @covers AndEvaluation
 */
class AndEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'and';

    public function andDataProvider(): array
    {
        return [
            [true, []],
            
            [true, [$this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'false')]],

            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],

            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],
            [false, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
        ];
    }

    /**
     * @test
     * @dataProvider andDataProvider
     */
    public function and(bool $expectedResult, array $config): void
    {
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
