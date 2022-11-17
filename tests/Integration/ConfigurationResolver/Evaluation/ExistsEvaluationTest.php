<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\ExistsEvaluation;

/**
 * @covers ExistsEvaluation
 */
class ExistsEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
    }

    /** @test */
    public function existsEvalTrue()
    {
        $config = [
            'field1' => [
                'exists' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function existsEvalFalse()
    {
        $config = [
            'field4' => [
                'exists' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function doesNotExistEvalTrue()
    {
        $config = [
            'field4' => [
                'exists' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function doesNotExistEvalFalse()
    {
        $config = [
            'field1' => [
                'exists' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
