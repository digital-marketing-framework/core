<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\ComparisonCondition;
use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ComparisonCondition::class)]
class ComparisonConditionTest extends ConditionTestBase
{
    protected const KEYWORD = 'comparison';

    #[Test]
    public function comparisonTrue(): void
    {
        $config = $this->getComparisonConfiguration(
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            null,
            'equals'
        );
        $result = $this->processCondition($config);
        $this->assertTrue($result);
    }

    #[Test]
    public function comparisonFalse(): void
    {
        $config = $this->getComparisonConfiguration(
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value1'], 'constant'),
            $this->getValueConfiguration([ConstantValueSource::KEY_VALUE => 'value2'], 'constant'),
            null,
            'equals'
        );
        $result = $this->processCondition($config);
        $this->assertFalse($result);
    }
}
