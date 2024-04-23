<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\Condition;

use DigitalMarketingFramework\Core\DataProcessor\Condition\TrueCondition;

class TrueConditionTest extends ConditionTest
{
    protected const CLASS_NAME = TrueCondition::class;

    protected const KEYWORD = 'true';

    /** @test */
    public function true(): void
    {
        $result = $this->processCondition([]);
        $this->assertTrue($result);
    }
}
