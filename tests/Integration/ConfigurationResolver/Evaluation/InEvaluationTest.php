<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\InEvaluation;

/**
 * @covers InEvaluation
 */
class InEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function nullIn(): void
    {
        $config = [
            'field1' => [
                'in' => '4,5,6',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function nullInList(): void
    {
        $config = [
            'field1' => [
                'in' => [
                    'list' => [4, 5, 6,],
                ]
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function in(): void
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => '4,5,6',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function inList(): void
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => [
                    4, 5, 6,
                    'list' => [4, 5, 6,],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notIn(): void
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => '4,6,7',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notInList(): void
    {
        $this->data['field1'] = 5;
        $config = [
            'field1' => [
                'in' => [
                    'list' => [4,6,7],
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
