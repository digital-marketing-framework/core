<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Condition\NotCondition
 */
class NotConditionTest extends ConditionTest
{
    protected const KEYWORD = 'not';

    /** @test */
    public function notTrue(): void
    {
        $config = $this->getConditionConfiguration([], 'true');
        $result = $this->processCondition($config);
        $this->assertFalse($result);
    }

    /** @test */
    public function notFalse(): void
    {
        $config = $this->getConditionConfiguration([], 'false');
        $result = $this->processCondition($config);
        $this->assertTrue($result);
    }
}
