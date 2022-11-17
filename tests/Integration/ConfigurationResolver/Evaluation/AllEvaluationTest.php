<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\Evaluation;

use DigitalMarketingFramework\Core\ConfigurationResolver\Evaluation\AnyEvaluation;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers AnyEvaluation
 */
class AllEvaluationTest extends AbstractEvaluationTest
{
    /** @test */
    public function allOfMultiValueRegexpEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'field1' => [
                'all' => [
                    'regexp' => 'value[0-9]',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfMultiValueEqualsEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'field1' => [
                'all' => [
                    'regexp' => 'value[12]'
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfEmptyMultiValueEqualsEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([]);
        $config = [
            'field1' => [
                'all' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfNonExistentFieldEqualsEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue(['value1', 'value2', 'value3']);
        $config = [
            'field2' => [
                'all' => [
                    'regexp' => 'value[0-9]',
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfScalarValueEqualsEvalTrue(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                'all' => 'value1',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfScalarValueEqualsEvalFalse(): void
    {
        $this->data['field1'] = 'value1';
        $config = [
            'field1' => [
                'all' => 'value2',
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesNoneEvalTrue(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 42,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesOneEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([5, 7, 13]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function allOfMultiValueEqualsNotMatchesAllEvalFalse(): void
    {
        $this->data['field1'] = new MultiValue([7, 7, 7]);
        $config = [
            'field1' => [
                'all' => [
                    'not' => 7,
                ],
            ],
        ];
        $result = $this->runEvaluationProcess($config);
        $this->assertFalse($result);
    }
}
