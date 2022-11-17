<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\NotEvaluation;

/**
 * @covers NotEvaluation
 */
class NotEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
    }

    /** @test */
    public function notUnaryEvalTrue(): void
    {
        $config = [
            'not' => '0',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notUnaryEvalFalse(): void
    {
        $config = [
            'not' => '1',
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFieldEqualsEvalTrue(): void
    {
        $config = [
            'not' => [
                'field1' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notFieldEqualsEvalFalse(): void
    {
        $config = [
            'not' => [
                'field1' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function fieldNotEqualsEvalTrue(): void
    {
        $config = [
            'field1' => [
                'not' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function fieldNotEqualsEvalFalse(): void
    {
        $config = [
            'field1' => [
                'not' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
