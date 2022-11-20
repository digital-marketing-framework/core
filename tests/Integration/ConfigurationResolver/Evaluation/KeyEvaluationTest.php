<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\KeyEvaluation;

/**
 * @covers KeyEvaluation
 */
class KeyEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function keyWithFieldEvalTrue(): void
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
    public function keyWithFieldEvalFalse(): void
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
    public function keyWithoutFieldEvalTrue(): void
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
    public function keyWithoutFieldEvalFalse(): void
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
