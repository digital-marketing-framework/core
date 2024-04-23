<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\Condition;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\Condition\TrueCondition
 */
class TrueConditionTest extends ConditionTest
{
    protected const KEYWORD = 'true';

    /** @test */
    public function true(): void
    {
        $result = $this->processCondition([]);
        $this->assertTrue($result);
    }
}
