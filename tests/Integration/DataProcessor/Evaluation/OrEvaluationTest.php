<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\OrEvaluation;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\OrEvaluation
 */
class OrEvaluationTest extends EvaluationTest
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
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                OrEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
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
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
