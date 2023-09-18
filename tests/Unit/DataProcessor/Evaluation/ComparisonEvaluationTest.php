<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Evaluation;

use DigitalMarketingFramework\Core\DataProcessor\Evaluation\ComparisonEvaluation;

class ComparisonEvaluationTest extends EvaluationTest
{
    protected const CLASS_NAME = ComparisonEvaluation::class;

    protected const KEYWORD = 'comparison';

    /** @test */
    public function comparisonTrue(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processComparison')->with($config)->willReturn(true);
        $result = $this->processEvaluation($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function comparisonFalse(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processComparison')->with($config)->willReturn(false);
        $result = $this->processEvaluation($config);
        $this->assertFalse($result);
    }
}
