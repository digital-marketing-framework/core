<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Evaluation\ComparisonEvaluation
 */
class ComparisonEvaluationTest extends EvaluationTest
{
    protected const KEYWORD = 'comparison';

    /** @test */
    public function comparisonTrue(): void
    {
        $config = $this->getComparisonConfiguration(
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            null,
            'equals'
        );
        $result = $this->processEvaluation($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function comparisonFalse(): void
    {
        $config = $this->getComparisonConfiguration(
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
            null,
            'equals'
        );
        $result = $this->processEvaluation($config);
        $this->assertFalse($result);
    }
}
