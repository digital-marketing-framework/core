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
    public function existsEvalTrue(): void
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
    public function existsEvalFalse(): void
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
    public function doesNotExistEvalTrue(): void
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
    public function doesNotExistEvalFalse(): void
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
