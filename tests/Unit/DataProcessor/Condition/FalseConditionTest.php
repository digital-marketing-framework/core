<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\FalseCondition;

class FalseConditionTest extends ConditionTest
{
    protected const CLASS_NAME = FalseCondition::class;

    protected const KEYWORD = 'false';

    /** @test */
    public function false(): void
    {
        $result = $this->processCondition([]);
        $this->assertFalse($result);
    }
}
