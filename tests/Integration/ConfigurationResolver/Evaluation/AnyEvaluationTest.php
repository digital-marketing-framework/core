<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers AnyEvaluation
 */
class AnyEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function anyOfMultiValueEqualsEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => 42,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfEmptyMultiValueEqualsEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([]);
        $config = [
            'field1' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfNonExistentFieldEqualsEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field2' => [
                'any' => 7,
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfScalarValueEqualsEvalTrue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                'any' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfScalarValueEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                'any' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesNoneEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 42,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesOneEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function anyOfMultiValueEqualsNotMatchesAllEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([7, 7, 7]);
        $config = [
            'field1' => [
                'any' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
