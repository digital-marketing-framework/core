<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\InEvaluation;

/**
 * @covers InEvaluation
 */
class InEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function nullIn()
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
    public function nullInList()
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
    public function in()
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
    public function inList()
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
    public function notIn()
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
    public function notInList()
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
