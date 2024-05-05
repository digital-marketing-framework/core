<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Condition\FalseCondition
 */
class FalseConditionTest extends ConditionTest
{
    protected const KEYWORD = 'false';

    /** @test */
    public function false(): void
    {
        $result = $this->processCondition([]);
        $this->assertFalse($result);
    }
}
