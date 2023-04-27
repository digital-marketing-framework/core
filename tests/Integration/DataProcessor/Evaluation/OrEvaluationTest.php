<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\OrEvaluation;

/**
 * @covers OrEvaluation
 */
class OrEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'or';

    public function orDataProvider(): array
    {
        return [
            [true, []],
            
            [true, [$this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'false')]],

            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],

            [false, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [true, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false'), $this->getEvaluationConfiguration([], 'true')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'false')]],
            [true, [$this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true'), $this->getEvaluationConfiguration([], 'true')]],
        ];
    }

    /**
     * @test
     * @dataProvider orDataProvider
     */
    public function or(bool $expectedResult, array $config): void
    {
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
