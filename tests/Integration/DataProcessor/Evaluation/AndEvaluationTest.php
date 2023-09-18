<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\AndEvaluation
 */
class AndEvaluationTest extends EvaluationTest
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
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                ],
            ]],

            [true, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                ],
            ]],

            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id3', 30),
                ],
            ]],
            [false, [
                AndEvaluation::KEY_EVALUATIONS => [
                    'id1' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id1', 10),
                    'id2' => $this->createListItem($this->getEvaluationConfiguration([], 'true'), 'id2', 20),
                    'id3' => $this->createListItem($this->getEvaluationConfiguration([], 'false'), 'id3', 30),
                ],
            ]],
            [true, [
                AndEvaluation::KEY_EVALUATIONS => [
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
     * @dataProvider andDataProvider
     */
    public function and(bool $expectedResult, array $config): void
    {
        $result = $this->processEvaluation($config);
        $this->assertEquals($expectedResult, $result);
    }
}
