<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\ComparisonCondition;

class ComparisonConditionTest extends ConditionTest
{
    protected const CLASS_NAME = ComparisonCondition::class;

    protected const KEYWORD = 'comparison';

    /** @test */
    public function comparisonTrue(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processComparison')->with($config)->willReturn(true);
        $result = $this->processCondition($config);
        $this->assertTrue($result);
    }

    /** @test */
    public function comparisonFalse(): void
    {
        $config = [
            'configKey1' => 'configValue1',
        ];
        $this->dataProcessor->method('processComparison')->with($config)->willReturn(false);
        $result = $this->processCondition($config);
        $this->assertFalse($result);
    }
}
