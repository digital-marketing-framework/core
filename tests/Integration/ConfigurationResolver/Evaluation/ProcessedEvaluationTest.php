<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\ProcessedEvaluation;

/**
 * @covers ProcessedEvaluation
 */
class ProcessedEvaluationTest extends AbstractEvaluationTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->data = ['field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'];
    }

    /** @test */
    public function processedInEvaluationEvalTrue()
    {
        $config = [
            1 => [ 'field1' => 'value1', ],
            2 => [
                'field1' => [
                    'processed' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function processedInEvaluationEvalFalse()
    {
        $config = [
            1 => [ 'field1' => 'value1', ],
            2 => [
                'field1' => [
                    'processed' => false,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notProcessedInEvaluationEvalTrue()
    {
        $config = [
            1 => [ 'field2' => 'value2', ],
            2 => [
                'field1' => [
                    'processed' => false,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notProcessedInEvaluationEvalFalse()
    {
        $config = [
            1 => [ 'field2' => 'value2', ],
            2 => [
                'field1' => [
                    'processed' => true,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function processedBeforeEvalTrue()
    {
        $this->fieldTracker->markAsProcessed('field1');
        $config = [
            'field1' => [
                'processed' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function processedBeforeEvalFalse()
    {
        $this->fieldTracker->markAsProcessed('field1');
        $config = [
            'field1' => [
                'processed' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notProcessedBeforeEvalTrue()
    {
        $this->fieldTracker->markAsProcessed('field2');
        $config = [
            'field1' => [
                'processed' => false,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function notProcessedBeforeEvalFalse()
    {
        $this->fieldTracker->markAsProcessed('field2');
        $config = [
            'field1' => [
                'processed' => true,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
