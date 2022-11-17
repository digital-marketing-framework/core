<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\KeyEvaluation;

/**
 * @covers KeyEvaluation
 */
class KeyEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function keyWithFieldEvalTrue()
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'field1' => [
                'key' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function keyWithFieldEvalFalse()
    {
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
        $config = [
            'field1' => [
                'key' => 'field2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function keyWithoutFieldEvalTrue()
    {
        $config = [
            'field1' => [
                'key' => 'field1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function keyWithoutFieldEvalFalse()
    {
        $config = [
            'field1' => [
                'key' => 'field2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
